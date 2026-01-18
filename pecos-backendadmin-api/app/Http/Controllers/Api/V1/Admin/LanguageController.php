<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\ProductTranslation;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * Get all languages
     */
    public function index()
    {
        $languages = Language::orderBy('sort_order')->get();

        // Add translation stats
        $languages = $languages->map(function ($lang) {
            $totalKeys = TranslationKey::count();
            $translatedKeys = Translation::where('language_id', $lang->id)->count();

            $lang->translation_progress = $totalKeys > 0
                ? round(($translatedKeys / $totalKeys) * 100, 1)
                : 0;
            $lang->translated_keys = $translatedKeys;
            $lang->total_keys = $totalKeys;

            return $lang;
        });

        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    }

    /**
     * Get active languages (for frontend)
     */
    public function active()
    {
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    }

    /**
     * Get language by code
     */
    public function show($code)
    {
        $language = Language::where('code', $code)
            ->orWhere('id', $code)
            ->first();

        if (!$language) {
            return response()->json(['error' => 'Language not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $language
        ]);
    }

    /**
     * Create new language
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'locale' => 'required|string|max:20|unique:languages,locale',
            'name' => 'required|string|max:100',
            'native_name' => 'required|string|max:100',
            'flag_icon' => 'nullable|string|max:50',
            'direction' => 'in:ltr,rtl',
            'is_active' => 'boolean',
        ]);

        $language = Language::create([
            'code' => strtolower($request->code),
            'locale' => $request->locale,
            'name' => $request->name,
            'native_name' => $request->native_name,
            'flag_icon' => $request->flag_icon,
            'direction' => $request->direction ?? 'ltr',
            'is_active' => $request->is_active ?? true,
            'is_default' => false,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Language created',
            'data' => $language
        ], 201);
    }

    /**
     * Update language
     */
    public function update(Request $request, $id)
    {
        $language = Language::find($id);
        if (!$language) {
            return response()->json(['error' => 'Language not found'], 404);
        }

        $request->validate([
            'code' => 'string|max:10|unique:languages,code,' . $id,
            'locale' => 'string|max:20|unique:languages,locale,' . $id,
            'name' => 'string|max:100',
            'native_name' => 'string|max:100',
            'flag_icon' => 'nullable|string|max:50',
            'direction' => 'in:ltr,rtl',
            'is_active' => 'boolean',
        ]);

        $language->update($request->only([
            'code', 'locale', 'name', 'native_name', 'flag_icon',
            'direction', 'is_active', 'sort_order'
        ]));

        return response()->json(['success' => true, 'message' => 'Language updated']);
    }

    /**
     * Set default language
     */
    public function setDefault($id)
    {
        $language = Language::find($id);
        if (!$language) {
            return response()->json(['error' => 'Language not found'], 404);
        }

        $language->setAsDefault();

        return response()->json(['success' => true, 'message' => 'Default language set']);
    }

    /**
     * Delete language
     */
    public function destroy($id)
    {
        $language = Language::find($id);
        if (!$language) {
            return response()->json(['error' => 'Language not found'], 404);
        }

        if ($language->is_default) {
            return response()->json(['error' => 'Cannot delete default language'], 400);
        }

        $language->delete();

        return response()->json(['success' => true, 'message' => 'Language deleted']);
    }

    // =====================
    // TRANSLATION MANAGEMENT
    // =====================

    /**
     * Get all translation groups
     */
    public function groups()
    {
        $groups = TranslationKey::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Get translation keys for a group
     */
    public function keys(Request $request, $group)
    {
        $keys = TranslationKey::where('group', $group)
            ->orderBy('key')
            ->get();

        // If language_id is provided, include translations
        if ($request->has('language_id')) {
            $languageId = $request->language_id;
            $keys = $keys->map(function ($key) use ($languageId) {
                $translation = Translation::where('translation_key_id', $key->id)
                    ->where('language_id', $languageId)
                    ->first();
                $key->translation = $translation?->value ?? '';
                $key->is_reviewed = $translation?->is_reviewed ?? false;
                return $key;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $keys
        ]);
    }

    /**
     * Get translations for a language
     */
    public function translations(Request $request, $languageId)
    {
        $query = Translation::where('language_id', $languageId)
            ->with('translationKey');

        if ($request->has('group')) {
            $query->whereHas('translationKey', function ($q) use ($request) {
                $q->where('group', $request->group);
            });
        }

        if ($request->has('unreviewed') && $request->unreviewed) {
            $query->where('is_reviewed', false);
        }

        $translations = $query->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'group' => $t->translationKey->group,
                'key' => $t->translationKey->key,
                'value' => $t->value,
                'is_reviewed' => $t->is_reviewed,
                'translated_by' => $t->translated_by,
                'updated_at' => $t->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $translations
        ]);
    }

    /**
     * Create or update translation key
     */
    public function storeKey(Request $request)
    {
        $request->validate([
            'group' => 'required|string|max:100',
            'key' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_html' => 'boolean',
        ]);

        $translationKey = TranslationKey::updateOrCreate(
            ['group' => $request->group, 'key' => $request->key],
            [
                'description' => $request->description,
                'is_html' => $request->is_html ?? false,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Translation key saved',
            'data' => $translationKey
        ]);
    }

    /**
     * Save translation
     */
    public function saveTranslation(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'group' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        $translation = Translation::setTranslation(
            $request->group,
            $request->key,
            $request->language_id,
            $request->value,
            $request->translated_by ?? 'manual'
        );

        return response()->json([
            'success' => true,
            'message' => 'Translation saved',
            'data' => $translation
        ]);
    }

    /**
     * Bulk save translations
     */
    public function bulkSaveTranslations(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'translations' => 'required|array',
            'translations.*.group' => 'required|string',
            'translations.*.key' => 'required|string',
            'translations.*.value' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($request->translations as $t) {
                Translation::setTranslation(
                    $t['group'],
                    $t['key'],
                    $request->language_id,
                    $t['value'],
                    'bulk_import'
                );
                $count++;
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Saved {$count} translations"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save translations: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark translation as reviewed
     */
    public function markReviewed($id)
    {
        $translation = Translation::find($id);
        if (!$translation) {
            return response()->json(['error' => 'Translation not found'], 404);
        }

        $translation->markReviewed();

        return response()->json(['success' => true, 'message' => 'Translation marked as reviewed']);
    }

    /**
     * Delete translation key
     */
    public function destroyKey($id)
    {
        $key = TranslationKey::find($id);
        if (!$key) {
            return response()->json(['error' => 'Translation key not found'], 404);
        }

        $key->delete();

        return response()->json(['success' => true, 'message' => 'Translation key deleted']);
    }

    // =====================
    // PRODUCT TRANSLATIONS
    // =====================

    /**
     * Get product translations
     */
    public function productTranslations($productId)
    {
        $translations = ProductTranslation::where('product_id', $productId)
            ->with('language')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $translations
        ]);
    }

    /**
     * Save product translation
     */
    public function saveProductTranslation(Request $request, $productId)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $translation = ProductTranslation::updateOrCreate(
            ['product_id' => $productId, 'language_id' => $request->language_id],
            $request->only(['name', 'description', 'short_description', 'meta_title', 'meta_description', 'meta_keywords'])
        );

        return response()->json([
            'success' => true,
            'message' => 'Product translation saved',
            'data' => $translation
        ]);
    }

    // =====================
    // CATEGORY TRANSLATIONS
    // =====================

    /**
     * Get category translations
     */
    public function categoryTranslations($categoryId)
    {
        $translations = CategoryTranslation::where('category_id', $categoryId)
            ->with('language')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $translations
        ]);
    }

    /**
     * Save category translation
     */
    public function saveCategoryTranslation(Request $request, $categoryId)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $translation = CategoryTranslation::updateOrCreate(
            ['category_id' => $categoryId, 'language_id' => $request->language_id],
            $request->only(['name', 'description', 'meta_title', 'meta_description'])
        );

        return response()->json([
            'success' => true,
            'message' => 'Category translation saved',
            'data' => $translation
        ]);
    }

    // =====================
    // FRONTEND API
    // =====================

    /**
     * Get all translations for a locale (frontend use)
     */
    public function getLocaleTranslations($locale)
    {
        $language = Language::where('locale', $locale)
            ->orWhere('code', $locale)
            ->where('is_active', true)
            ->first();

        if (!$language) {
            return response()->json(['error' => 'Language not found or not active'], 404);
        }

        $translations = DB::table('translations')
            ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
            ->where('translations.language_id', $language->id)
            ->select('translation_keys.group', 'translation_keys.key', 'translations.value')
            ->get();

        // Format as nested object: { group: { key: value } }
        $formatted = [];
        foreach ($translations as $t) {
            if (!isset($formatted[$t->group])) {
                $formatted[$t->group] = [];
            }
            $formatted[$t->group][$t->key] = $t->value;
        }

        return response()->json([
            'success' => true,
            'language' => $language,
            'translations' => $formatted
        ]);
    }

    /**
     * Get stats
     */
    public function stats()
    {
        $languages = Language::count();
        $activeLanguages = Language::where('is_active', true)->count();
        $totalKeys = TranslationKey::count();
        $totalTranslations = Translation::count();
        $unreviewedTranslations = Translation::where('is_reviewed', false)->count();
        $groups = TranslationKey::distinct('group')->count('group');

        return response()->json([
            'success' => true,
            'data' => [
                'languages' => $languages,
                'active_languages' => $activeLanguages,
                'total_keys' => $totalKeys,
                'total_translations' => $totalTranslations,
                'unreviewed_translations' => $unreviewedTranslations,
                'groups' => $groups,
            ]
        ]);
    }
}
