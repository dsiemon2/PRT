<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    /**
     * Get all currencies
     */
    public function index()
    {
        $currencies = DB::table('currencies')
            ->leftJoin('exchange_rates', 'currencies.id', '=', 'exchange_rates.currency_id')
            ->select('currencies.*', 'exchange_rates.rate', 'exchange_rates.fetched_at')
            ->orderBy('currencies.sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Get active currencies (for frontend)
     */
    public function active()
    {
        $currencies = DB::table('currencies')
            ->leftJoin('exchange_rates', 'currencies.id', '=', 'exchange_rates.currency_id')
            ->select('currencies.*', 'exchange_rates.rate')
            ->where('currencies.is_active', true)
            ->orderBy('currencies.sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Get currency by code
     */
    public function show($code)
    {
        $currency = DB::table('currencies')
            ->leftJoin('exchange_rates', 'currencies.id', '=', 'exchange_rates.currency_id')
            ->select('currencies.*', 'exchange_rates.rate', 'exchange_rates.fetched_at', 'exchange_rates.source')
            ->where('currencies.code', strtoupper($code))
            ->first();

        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $currency
        ]);
    }

    /**
     * Create new currency
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'in:before,after',
            'decimal_places' => 'integer|min:0|max:4',
            'is_active' => 'boolean',
            'rate' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $currencyId = DB::table('currencies')->insertGetId([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'symbol' => $request->symbol,
                'symbol_position' => $request->symbol_position ?? 'before',
                'decimal_places' => $request->decimal_places ?? 2,
                'decimal_separator' => $request->decimal_separator ?? '.',
                'thousand_separator' => $request->thousand_separator ?? ',',
                'is_active' => $request->is_active ?? true,
                'is_default' => false,
                'sort_order' => $request->sort_order ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Set exchange rate if provided
            if ($request->rate) {
                DB::table('exchange_rates')->insert([
                    'currency_id' => $currencyId,
                    'rate' => $request->rate,
                    'source' => 'manual',
                    'fetched_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Currency created',
                'data' => ['id' => $currencyId]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create currency: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update currency
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'string|size:3|unique:currencies,code,' . $id,
            'name' => 'string|max:100',
            'symbol' => 'string|max:10',
            'symbol_position' => 'in:before,after',
            'decimal_places' => 'integer|min:0|max:4',
            'is_active' => 'boolean',
        ]);

        $currency = DB::table('currencies')->where('id', $id)->first();
        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        DB::table('currencies')->where('id', $id)->update([
            'code' => strtoupper($request->code ?? $currency->code),
            'name' => $request->name ?? $currency->name,
            'symbol' => $request->symbol ?? $currency->symbol,
            'symbol_position' => $request->symbol_position ?? $currency->symbol_position,
            'decimal_places' => $request->decimal_places ?? $currency->decimal_places,
            'decimal_separator' => $request->decimal_separator ?? $currency->decimal_separator,
            'thousand_separator' => $request->thousand_separator ?? $currency->thousand_separator,
            'is_active' => $request->is_active ?? $currency->is_active,
            'sort_order' => $request->sort_order ?? $currency->sort_order,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Currency updated']);
    }

    /**
     * Set default currency
     */
    public function setDefault($id)
    {
        $currency = DB::table('currencies')->where('id', $id)->first();
        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        DB::table('currencies')->update(['is_default' => false]);
        DB::table('currencies')->where('id', $id)->update([
            'is_default' => true,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Default currency set']);
    }

    /**
     * Update exchange rate
     */
    public function updateRate(Request $request, $id)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0',
        ]);

        $currency = DB::table('currencies')->where('id', $id)->first();
        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        // Get existing rate for history
        $existing = DB::table('exchange_rates')->where('currency_id', $id)->first();
        if ($existing) {
            DB::table('exchange_rate_history')->insert([
                'currency_id' => $id,
                'rate' => $existing->rate,
                'source' => $existing->source,
                'recorded_at' => $existing->fetched_at ?? $existing->updated_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('exchange_rates')->updateOrInsert(
            ['currency_id' => $id],
            [
                'rate' => $request->rate,
                'source' => 'manual',
                'fetched_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Exchange rate updated']);
    }

    /**
     * Get exchange rate history
     */
    public function rateHistory($id)
    {
        $history = DB::table('exchange_rate_history')
            ->where('currency_id', $id)
            ->orderBy('recorded_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Fetch rates from external API
     */
    public function fetchRates()
    {
        // This is a placeholder for external API integration
        // Supports: exchangeratesapi.io, openexchangerates.org, fixer.io

        $apiKey = config('services.exchange_rates.api_key');
        $provider = config('services.exchange_rates.provider', 'exchangeratesapi');

        if (!$apiKey) {
            return response()->json(['error' => 'Exchange rate API not configured'], 400);
        }

        try {
            // Get base currency
            $baseCurrency = DB::table('currencies')->where('is_default', true)->first();
            if (!$baseCurrency) {
                return response()->json(['error' => 'No default currency set'], 400);
            }

            $currencies = DB::table('currencies')
                ->where('is_active', true)
                ->where('is_default', false)
                ->pluck('code')
                ->toArray();

            if (empty($currencies)) {
                return response()->json(['message' => 'No currencies to update']);
            }

            // Fetch from API (example with exchangeratesapi.io)
            $response = Http::get("https://api.exchangeratesapi.io/latest", [
                'access_key' => $apiKey,
                'base' => $baseCurrency->code,
                'symbols' => implode(',', $currencies),
            ]);

            if ($response->successful()) {
                $rates = $response->json()['rates'] ?? [];

                foreach ($rates as $code => $rate) {
                    $currency = DB::table('currencies')->where('code', $code)->first();
                    if ($currency) {
                        ExchangeRate::updateRate($currency->id, $rate, 'api');
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Exchange rates updated',
                    'updated' => count($rates)
                ]);
            }

            return response()->json(['error' => 'Failed to fetch rates from API'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching rates: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Convert amount between currencies
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $fromCurrency = DB::table('currencies')
            ->leftJoin('exchange_rates', 'currencies.id', '=', 'exchange_rates.currency_id')
            ->where('currencies.code', strtoupper($request->from))
            ->first();

        $toCurrency = DB::table('currencies')
            ->leftJoin('exchange_rates', 'currencies.id', '=', 'exchange_rates.currency_id')
            ->where('currencies.code', strtoupper($request->to))
            ->first();

        if (!$fromCurrency || !$toCurrency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        $fromRate = $fromCurrency->rate ?? 1;
        $toRate = $toCurrency->rate ?? 1;

        // Convert to base first, then to target
        $baseAmount = $fromRate > 0 ? $request->amount / $fromRate : $request->amount;
        $convertedAmount = $baseAmount * $toRate;

        return response()->json([
            'success' => true,
            'data' => [
                'amount' => $request->amount,
                'from' => $request->from,
                'to' => $request->to,
                'converted' => round($convertedAmount, $toCurrency->decimal_places ?? 2),
                'rate' => $toRate / $fromRate,
            ]
        ]);
    }

    /**
     * Delete currency
     */
    public function destroy($id)
    {
        $currency = DB::table('currencies')->where('id', $id)->first();
        if (!$currency) {
            return response()->json(['error' => 'Currency not found'], 404);
        }

        if ($currency->is_default) {
            return response()->json(['error' => 'Cannot delete default currency'], 400);
        }

        DB::table('currencies')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Currency deleted']);
    }
}
