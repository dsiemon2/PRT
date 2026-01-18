<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttributeType;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use App\Models\VariantImage;
use App\Models\VariantPriceRule;
use App\Models\VariantInventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VariantsController extends Controller
{
    // ==================
    // ATTRIBUTE TYPES
    // ==================

    public function attributeTypes(): JsonResponse
    {
        $types = ProductAttributeType::with('values')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $types]);
    }

    public function attributeType($id): JsonResponse
    {
        $type = ProductAttributeType::with('values')->findOrFail($id);
        return response()->json(['data' => $type]);
    }

    public function storeAttributeType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:product_attribute_types,code',
            'display_type' => 'string|in:dropdown,swatch,radio,buttons',
            'is_visible' => 'boolean',
            'is_variation' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $type = ProductAttributeType::create($validated);

        return response()->json(['data' => $type], 201);
    }

    public function updateAttributeType(Request $request, $id): JsonResponse
    {
        $type = ProductAttributeType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:product_attribute_types,code,' . $id,
            'display_type' => 'string|in:dropdown,swatch,radio,buttons',
            'is_visible' => 'boolean',
            'is_variation' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $type->update($validated);

        return response()->json(['data' => $type]);
    }

    public function deleteAttributeType($id): JsonResponse
    {
        $type = ProductAttributeType::findOrFail($id);
        $type->delete();

        return response()->json(['message' => 'Attribute type deleted']);
    }

    public function displayTypes(): JsonResponse
    {
        return response()->json(['data' => ProductAttributeType::getDisplayTypes()]);
    }

    // ==================
    // ATTRIBUTE VALUES
    // ==================

    public function storeAttributeValue(Request $request, $typeId): JsonResponse
    {
        $type = ProductAttributeType::findOrFail($typeId);

        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'swatch_value' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $validated['attribute_type_id'] = $type->id;
        $value = ProductAttributeValue::create($validated);

        return response()->json(['data' => $value], 201);
    }

    public function updateAttributeValue(Request $request, $id): JsonResponse
    {
        $value = ProductAttributeValue::findOrFail($id);

        $validated = $request->validate([
            'value' => 'string|max:255',
            'label' => 'nullable|string|max:255',
            'swatch_value' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $value->update($validated);

        return response()->json(['data' => $value]);
    }

    public function deleteAttributeValue($id): JsonResponse
    {
        $value = ProductAttributeValue::findOrFail($id);
        $value->delete();

        return response()->json(['message' => 'Attribute value deleted']);
    }

    // ==================
    // PRODUCT VARIANTS
    // ==================

    public function productVariants($productId): JsonResponse
    {
        $variants = ProductVariant::where('product_id', $productId)
            ->with(['attributeValues.attributeType', 'attributeValues.attributeValue', 'images', 'priceRules'])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $variants]);
    }

    public function variant($id): JsonResponse
    {
        $variant = ProductVariant::with([
            'attributeValues.attributeType',
            'attributeValues.attributeValue',
            'images',
            'priceRules',
            'product'
        ])->findOrFail($id);

        return response()->json(['data' => $variant]);
    }

    public function storeVariant(Request $request, $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'string|in:lb,kg,oz,g',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'dimension_unit' => 'string|in:in,cm,mm',
            'barcode' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'track_inventory' => 'boolean',
            'allow_backorder' => 'boolean',
            'sort_order' => 'integer',
            'attributes' => 'array',
            'attributes.*.attribute_type_id' => 'required|exists:product_attribute_types,id',
            'attributes.*.attribute_value_id' => 'required|exists:product_attribute_values,id',
        ]);

        DB::beginTransaction();
        try {
            $attributes = $validated['attributes'] ?? [];
            unset($validated['attributes']);

            $validated['product_id'] = $product->id;
            $variant = ProductVariant::create($validated);

            foreach ($attributes as $attr) {
                VariantAttributeValue::create([
                    'variant_id' => $variant->id,
                    'attribute_type_id' => $attr['attribute_type_id'],
                    'attribute_value_id' => $attr['attribute_value_id'],
                ]);
            }

            // Update product to have variants
            $product->update(['has_variants' => true]);

            DB::commit();

            return response()->json([
                'data' => $variant->load(['attributeValues.attributeType', 'attributeValues.attributeValue'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateVariant(Request $request, $id): JsonResponse
    {
        $variant = ProductVariant::findOrFail($id);

        $validated = $request->validate([
            'sku' => 'string|max:100|unique:product_variants,sku,' . $id,
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'string|in:lb,kg,oz,g',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'dimension_unit' => 'string|in:in,cm,mm',
            'barcode' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'track_inventory' => 'boolean',
            'allow_backorder' => 'boolean',
            'sort_order' => 'integer',
            'attributes' => 'array',
            'attributes.*.attribute_type_id' => 'required|exists:product_attribute_types,id',
            'attributes.*.attribute_value_id' => 'required|exists:product_attribute_values,id',
        ]);

        DB::beginTransaction();
        try {
            $attributes = $validated['attributes'] ?? null;
            unset($validated['attributes']);

            $variant->update($validated);

            if ($attributes !== null) {
                $variant->attributeValues()->delete();
                foreach ($attributes as $attr) {
                    VariantAttributeValue::create([
                        'variant_id' => $variant->id,
                        'attribute_type_id' => $attr['attribute_type_id'],
                        'attribute_value_id' => $attr['attribute_value_id'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'data' => $variant->fresh(['attributeValues.attributeType', 'attributeValues.attributeValue'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteVariant($id): JsonResponse
    {
        $variant = ProductVariant::findOrFail($id);
        $productId = $variant->product_id;

        $variant->delete();

        // Check if product still has variants
        $remainingVariants = ProductVariant::where('product_id', $productId)->count();
        if ($remainingVariants === 0) {
            Product::where('id', $productId)->update(['has_variants' => false]);
        }

        return response()->json(['message' => 'Variant deleted']);
    }

    // ==================
    // VARIANT IMAGES
    // ==================

    public function variantImages($variantId): JsonResponse
    {
        $images = VariantImage::where('variant_id', $variantId)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $images]);
    }

    public function storeVariantImage(Request $request, $variantId): JsonResponse
    {
        $variant = ProductVariant::findOrFail($variantId);

        $validated = $request->validate([
            'image_url' => 'required|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['variant_id'] = $variant->id;

        // If this is primary, unset other primary images
        if ($validated['is_primary'] ?? false) {
            VariantImage::where('variant_id', $variant->id)->update(['is_primary' => false]);
        }

        $image = VariantImage::create($validated);

        return response()->json(['data' => $image], 201);
    }

    public function deleteVariantImage($id): JsonResponse
    {
        $image = VariantImage::findOrFail($id);
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    // ==================
    // PRICE RULES
    // ==================

    public function priceRules($variantId): JsonResponse
    {
        $rules = VariantPriceRule::where('variant_id', $variantId)
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json(['data' => $rules]);
    }

    public function storePriceRule(Request $request, $variantId): JsonResponse
    {
        $variant = ProductVariant::findOrFail($variantId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rule_type' => 'required|string|in:quantity_discount,customer_group,date_range',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'customer_group' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ]);

        $validated['variant_id'] = $variant->id;
        $rule = VariantPriceRule::create($validated);

        return response()->json(['data' => $rule], 201);
    }

    public function updatePriceRule(Request $request, $id): JsonResponse
    {
        $rule = VariantPriceRule::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'rule_type' => 'string|in:quantity_discount,customer_group,date_range',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'customer_group' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ]);

        $rule->update($validated);

        return response()->json(['data' => $rule]);
    }

    public function deletePriceRule($id): JsonResponse
    {
        $rule = VariantPriceRule::findOrFail($id);
        $rule->delete();

        return response()->json(['message' => 'Price rule deleted']);
    }

    public function priceRuleTypes(): JsonResponse
    {
        return response()->json(['data' => VariantPriceRule::getRuleTypes()]);
    }

    // ==================
    // INVENTORY
    // ==================

    public function adjustInventory(Request $request, $variantId): JsonResponse
    {
        $variant = ProductVariant::findOrFail($variantId);

        $validated = $request->validate([
            'quantity' => 'required|integer',
            'action' => 'required|string|in:add,remove,adjust',
            'reason' => 'nullable|string|max:500',
        ]);

        $quantity = $validated['action'] === 'remove' ? -abs($validated['quantity']) : $validated['quantity'];

        $variant->adjustStock(
            $quantity,
            $validated['action'],
            $validated['reason'] ?? null,
            'manual_adjustment',
            null,
            auth()->id()
        );

        return response()->json([
            'data' => $variant->fresh(),
            'message' => 'Inventory adjusted'
        ]);
    }

    public function inventoryLogs($variantId): JsonResponse
    {
        $logs = VariantInventoryLog::where('variant_id', $variantId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($logs);
    }

    public function bulkUpdateInventory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'updates' => 'required|array',
            'updates.*.variant_id' => 'required|exists:product_variants,id',
            'updates.*.quantity' => 'required|integer|min:0',
        ]);

        $updated = 0;
        foreach ($validated['updates'] as $update) {
            $variant = ProductVariant::find($update['variant_id']);
            if ($variant) {
                $diff = $update['quantity'] - $variant->stock_quantity;
                $variant->adjustStock($diff, 'adjust', 'Bulk inventory update', 'bulk_update', null, auth()->id());
                $updated++;
            }
        }

        return response()->json([
            'message' => "$updated variants updated",
            'updated_count' => $updated
        ]);
    }

    // ==================
    // VARIANT MATRIX
    // ==================

    public function generateVariantMatrix(Request $request, $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'attribute_type_ids' => 'required|array|min:1|max:3',
            'attribute_type_ids.*' => 'exists:product_attribute_types,id',
        ]);

        $attributeTypes = ProductAttributeType::whereIn('id', $validated['attribute_type_ids'])
            ->with('activeValues')
            ->get();

        // Generate all combinations
        $combinations = [[]];
        foreach ($attributeTypes as $type) {
            $newCombinations = [];
            foreach ($combinations as $combo) {
                foreach ($type->activeValues as $value) {
                    $newCombinations[] = array_merge($combo, [
                        [
                            'attribute_type_id' => $type->id,
                            'attribute_type_name' => $type->name,
                            'attribute_value_id' => $value->id,
                            'attribute_value' => $value->display_label,
                        ]
                    ]);
                }
            }
            $combinations = $newCombinations;
        }

        // Generate suggested SKUs
        $matrix = [];
        foreach ($combinations as $i => $combo) {
            $skuParts = [$product->sku ?? 'PROD'];
            $nameParts = [];
            foreach ($combo as $attr) {
                $skuParts[] = strtoupper(substr($attr['attribute_value'], 0, 3));
                $nameParts[] = $attr['attribute_value'];
            }

            $matrix[] = [
                'suggested_sku' => implode('-', $skuParts),
                'suggested_name' => $product->name . ' - ' . implode(' / ', $nameParts),
                'attributes' => $combo,
            ];
        }

        return response()->json([
            'data' => [
                'product' => $product,
                'attribute_types' => $attributeTypes,
                'matrix' => $matrix,
                'total_combinations' => count($matrix),
            ]
        ]);
    }

    public function bulkCreateVariants(Request $request, $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|max:100|distinct',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'integer|min:0',
            'variants.*.attributes' => 'required|array|min:1',
            'variants.*.attributes.*.attribute_type_id' => 'required|exists:product_attribute_types,id',
            'variants.*.attributes.*.attribute_value_id' => 'required|exists:product_attribute_values,id',
        ]);

        DB::beginTransaction();
        try {
            $created = [];

            foreach ($validated['variants'] as $variantData) {
                $attributes = $variantData['attributes'];
                unset($variantData['attributes']);

                $variantData['product_id'] = $product->id;
                $variant = ProductVariant::create($variantData);

                foreach ($attributes as $attr) {
                    VariantAttributeValue::create([
                        'variant_id' => $variant->id,
                        'attribute_type_id' => $attr['attribute_type_id'],
                        'attribute_value_id' => $attr['attribute_value_id'],
                    ]);
                }

                $created[] = $variant->load(['attributeValues.attributeType', 'attributeValues.attributeValue']);
            }

            $product->update(['has_variants' => true]);

            DB::commit();

            return response()->json([
                'data' => $created,
                'message' => count($created) . ' variants created'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==================
    // STATS
    // ==================

    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'attribute_types' => ProductAttributeType::count(),
                'total_values' => ProductAttributeValue::count(),
                'total_variants' => ProductVariant::count(),
                'active_variants' => ProductVariant::where('is_active', true)->count(),
                'low_stock_variants' => ProductVariant::where('track_inventory', true)
                    ->whereColumn('stock_quantity', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
                    ->where('stock_quantity', '>', 0)
                    ->count(),
                'out_of_stock_variants' => ProductVariant::where('track_inventory', true)
                    ->where('stock_quantity', '<=', 0)
                    ->count(),
                'products_with_variants' => Product::where('has_variants', true)->count(),
            ]
        ]);
    }
}
