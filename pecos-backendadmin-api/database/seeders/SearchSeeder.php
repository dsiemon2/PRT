<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchSeeder extends Seeder
{
    public function run(): void
    {
        // Create default facets
        $facets = [
            [
                'name' => 'Category',
                'code' => 'category',
                'type' => 'category',
                'attribute_name' => null,
                'options' => null,
                'is_active' => true,
                'is_collapsed' => false,
                'sort_order' => 1,
                'max_options' => 10,
                'show_count' => true,
            ],
            [
                'name' => 'Price Range',
                'code' => 'price',
                'type' => 'price_range',
                'attribute_name' => 'price',
                'options' => json_encode([
                    ['label' => 'Under $25', 'min' => 0, 'max' => 25],
                    ['label' => '$25 - $50', 'min' => 25, 'max' => 50],
                    ['label' => '$50 - $100', 'min' => 50, 'max' => 100],
                    ['label' => '$100 - $200', 'min' => 100, 'max' => 200],
                    ['label' => 'Over $200', 'min' => 200, 'max' => null],
                ]),
                'is_active' => true,
                'is_collapsed' => false,
                'sort_order' => 2,
                'max_options' => 10,
                'show_count' => true,
            ],
            [
                'name' => 'Brand',
                'code' => 'brand',
                'type' => 'brand',
                'attribute_name' => 'brand',
                'options' => null,
                'is_active' => true,
                'is_collapsed' => false,
                'sort_order' => 3,
                'max_options' => 15,
                'show_count' => true,
            ],
            [
                'name' => 'Customer Rating',
                'code' => 'rating',
                'type' => 'rating',
                'attribute_name' => 'average_rating',
                'options' => json_encode([
                    ['label' => '4 Stars & Up', 'min' => 4],
                    ['label' => '3 Stars & Up', 'min' => 3],
                    ['label' => '2 Stars & Up', 'min' => 2],
                    ['label' => '1 Star & Up', 'min' => 1],
                ]),
                'is_active' => true,
                'is_collapsed' => true,
                'sort_order' => 4,
                'max_options' => 5,
                'show_count' => true,
            ],
            [
                'name' => 'Availability',
                'code' => 'availability',
                'type' => 'availability',
                'attribute_name' => 'stock_quantity',
                'options' => json_encode([
                    ['label' => 'In Stock', 'value' => 'in_stock'],
                    ['label' => 'Out of Stock', 'value' => 'out_of_stock'],
                ]),
                'is_active' => true,
                'is_collapsed' => true,
                'sort_order' => 5,
                'max_options' => 3,
                'show_count' => true,
            ],
            [
                'name' => 'Size',
                'code' => 'size',
                'type' => 'size',
                'attribute_name' => 'size',
                'options' => null,
                'is_active' => true,
                'is_collapsed' => true,
                'sort_order' => 6,
                'max_options' => 20,
                'show_count' => true,
            ],
        ];

        foreach ($facets as $facet) {
            DB::table('search_facets')->insert(array_merge($facet, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create common synonyms
        $synonyms = [
            ['term' => 'shirt', 'synonyms' => 'top, blouse, tee, t-shirt'],
            ['term' => 'pants', 'synonyms' => 'trousers, jeans, slacks, bottoms'],
            ['term' => 'shoes', 'synonyms' => 'footwear, sneakers, boots, sandals'],
            ['term' => 'jacket', 'synonyms' => 'coat, blazer, outerwear'],
            ['term' => 'bag', 'synonyms' => 'purse, handbag, tote, backpack'],
            ['term' => 'cheap', 'synonyms' => 'affordable, budget, low price, sale'],
            ['term' => 'premium', 'synonyms' => 'luxury, high-end, quality, exclusive'],
            ['term' => 'small', 'synonyms' => 'xs, extra small, petite'],
            ['term' => 'large', 'synonyms' => 'xl, extra large, big, plus size'],
            ['term' => 'red', 'synonyms' => 'crimson, scarlet, ruby, burgundy'],
        ];

        foreach ($synonyms as $synonym) {
            DB::table('search_synonyms')->insert(array_merge($synonym, [
                'is_bidirectional' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create sample popular searches
        $popularSearches = [
            ['query' => 'new arrivals', 'search_count' => 1500, 'click_count' => 1200, 'is_featured' => true],
            ['query' => 'sale', 'search_count' => 2500, 'click_count' => 2000, 'is_featured' => true],
            ['query' => 'bestsellers', 'search_count' => 1800, 'click_count' => 1400, 'is_featured' => true],
            ['query' => 'gifts', 'search_count' => 1200, 'click_count' => 900, 'is_featured' => true],
            ['query' => 'clearance', 'search_count' => 900, 'click_count' => 700, 'is_featured' => false],
        ];

        foreach ($popularSearches as $search) {
            DB::table('popular_searches')->insert(array_merge($search, [
                'conversion_rate' => round(($search['click_count'] / $search['search_count']) * 10, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
