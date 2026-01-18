<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturedProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing featured products
        DB::table('featured_products')->truncate();

        // Featured products from different categories with real images
        $featuredUpcs = [
            '09-20-601-0250', // Men's Slip-ons (cat 58)
            '09-20-400-0421', // Men's Boots (cat 59)
            '09-21-323-0490', // Women's Slip-ons (cat 62)
            '09-21-220-0206', // Sandals (cat 65)
            '09-21-524-0370', // Fashion (cat 66)
            '09-21-532-0213', // Women's Boots (cat 67)
        ];

        $descriptions = [
            'Comfortable everyday slip-on shoes.',
            'Durable boots for work and outdoor adventures.',
            'Stylish and comfortable women\'s slip-ons.',
            'Perfect sandals for warm weather.',
            'Trendy fashion footwear.',
            'Quality women\'s boots for any occasion.',
        ];

        $sortOrder = 1;
        foreach ($featuredUpcs as $index => $upc) {
            $product = DB::table('products3')->where('UPC', $upc)->first();

            if ($product) {
                // Ensure featured products have stock
                DB::table('products3')->where('UPC', $upc)->update(['QTY' => 100]);

                DB::table('featured_products')->insert([
                    'upc' => $upc,
                    'label' => $product->ShortDescription ?? 'Featured Product',
                    'description' => $descriptions[$index] ?? 'Quality footwear at great prices.',
                    'sort_order' => $sortOrder++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
