<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Seed the currencies table.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => ',',
                'thousand_separator' => '.',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'CA$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'symbol_position' => 'before',
                'decimal_places' => 0,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 6,
            ],
            [
                'code' => 'MXN',
                'name' => 'Mexican Peso',
                'symbol' => 'MX$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 7,
            ],
        ];

        // Sample exchange rates (relative to USD)
        $exchangeRates = [
            'USD' => 1.00,
            'EUR' => 0.92,
            'GBP' => 0.79,
            'CAD' => 1.36,
            'AUD' => 1.53,
            'JPY' => 149.50,
            'MXN' => 17.15,
        ];

        foreach ($currencies as $currency) {
            $currencyId = DB::table('currencies')->insertGetId(array_merge($currency, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Add exchange rate
            if (isset($exchangeRates[$currency['code']])) {
                DB::table('exchange_rates')->insert([
                    'currency_id' => $currencyId,
                    'rate' => $exchangeRates[$currency['code']],
                    'source' => 'seeder',
                    'fetched_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
