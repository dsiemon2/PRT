<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the settings table with default values.
     */
    public function run(): void
    {
        $settings = [
            // Category Display Settings (defaults match current PRT3 implementation)
            ['setting_group' => 'category_display', 'setting_key' => 'category_display_style', 'setting_value' => 'cards', 'setting_type' => 'string'],
            ['setting_group' => 'category_display', 'setting_key' => 'category_cards_per_row_desktop', 'setting_value' => '3', 'setting_type' => 'number'],
            ['setting_group' => 'category_display', 'setting_key' => 'category_cards_per_row_tablet', 'setting_value' => '2', 'setting_type' => 'number'],
            ['setting_group' => 'category_display', 'setting_key' => 'category_cards_per_row_mobile', 'setting_value' => '1', 'setting_type' => 'number'],
            ['setting_group' => 'category_display', 'setting_key' => 'category_hover_effect', 'setting_value' => 'lift', 'setting_type' => 'string'],
            ['setting_group' => 'category_display', 'setting_key' => 'category_show_product_count', 'setting_value' => '1', 'setting_type' => 'boolean'],
            ['setting_group' => 'category_display', 'setting_key' => 'category_show_description', 'setting_value' => '1', 'setting_type' => 'boolean'],
            ['setting_group' => 'category_display', 'setting_key' => 'featured_category_ids', 'setting_value' => '59,67,65,58,62,66', 'setting_type' => 'string'],

            // Product Layout Settings (defaults match current PRT3 sidebar implementation)
            ['setting_group' => 'product_layout', 'setting_key' => 'product_layout_style', 'setting_value' => 'sidebar', 'setting_type' => 'string'],
            ['setting_group' => 'product_layout', 'setting_key' => 'category_bar_sticky', 'setting_value' => '0', 'setting_type' => 'boolean'],
            ['setting_group' => 'product_layout', 'setting_key' => 'category_bar_max_items', 'setting_value' => '6', 'setting_type' => 'number'],
            ['setting_group' => 'product_layout', 'setting_key' => 'category_bar_bg_color', 'setting_value' => '#f8f9fa', 'setting_type' => 'string'],
            ['setting_group' => 'product_layout', 'setting_key' => 'category_bar_text_color', 'setting_value' => '#333333', 'setting_type' => 'string'],
            ['setting_group' => 'product_layout', 'setting_key' => 'category_bar_hover_color', 'setting_value' => '#8B4513', 'setting_type' => 'string'],
            ['setting_group' => 'product_layout', 'setting_key' => 'show_subcategories_dropdown', 'setting_value' => '1', 'setting_type' => 'boolean'],
            ['setting_group' => 'product_layout', 'setting_key' => 'products_per_row_desktop', 'setting_value' => '3', 'setting_type' => 'number'],
            ['setting_group' => 'product_layout', 'setting_key' => 'products_per_row_tablet', 'setting_value' => '2', 'setting_type' => 'number'],
            ['setting_group' => 'product_layout', 'setting_key' => 'products_per_row_mobile', 'setting_value' => '1', 'setting_type' => 'number'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                [
                    'setting_group' => $setting['setting_group'],
                    'setting_key' => $setting['setting_key']
                ],
                [
                    'setting_value' => $setting['setting_value'],
                    'setting_type' => $setting['setting_type'],
                    'updated_at' => now()
                ]
            );
        }
    }
}
