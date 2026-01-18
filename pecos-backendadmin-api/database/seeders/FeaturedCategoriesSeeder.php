<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturedCategoriesSeeder extends Seeder
{
    /**
     * Seed the featured categories table with default data.
     * Matches the current hardcoded featured categories in PRT3.
     */
    public function run(): void
    {
        $featuredCategories = [
            [
                'category_id' => 59,
                'label' => "Men's Boots",
                'description' => "Durable men's boots built for work and outdoor activities. Classic styles and reliable construction.",
                'sort_order' => 1,
            ],
            [
                'category_id' => 67,
                'label' => "Women's Boots",
                'description' => "Stylish and comfortable women's boots. Perfect for any occasion from casual to work wear.",
                'sort_order' => 2,
            ],
            [
                'category_id' => 65,
                'label' => 'Sandals',
                'description' => 'Comfortable sandals for warm weather. Casual and dressy styles for every summer occasion.',
                'sort_order' => 3,
            ],
            [
                'category_id' => 58,
                'label' => "Men's Slip-ons",
                'description' => 'Easy on, easy off. Comfortable slip-on footwear for casual everyday wear.',
                'sort_order' => 4,
            ],
            [
                'category_id' => 62,
                'label' => "Women's Slip-ons",
                'description' => "Convenient and comfortable women's slip-on shoes. Perfect for busy lifestyles.",
                'sort_order' => 5,
            ],
            [
                'category_id' => 66,
                'label' => 'Fashion Shoes',
                'description' => 'Trendy and stylish footwear for fashion-conscious women. Stand out in style.',
                'sort_order' => 6,
            ],
        ];

        foreach ($featuredCategories as $category) {
            DB::table('featured_categories')->updateOrInsert(
                ['category_id' => $category['category_id']],
                array_merge($category, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
