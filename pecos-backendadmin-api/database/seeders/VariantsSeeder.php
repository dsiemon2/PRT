<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariantsSeeder extends Seeder
{
    public function run(): void
    {
        // Create default attribute types
        $attributeTypes = [
            [
                'name' => 'Size',
                'code' => 'size',
                'display_type' => 'buttons',
                'is_visible' => true,
                'is_variation' => true,
                'is_filterable' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Color',
                'code' => 'color',
                'display_type' => 'swatch',
                'is_visible' => true,
                'is_variation' => true,
                'is_filterable' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Material',
                'code' => 'material',
                'display_type' => 'dropdown',
                'is_visible' => true,
                'is_variation' => true,
                'is_filterable' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Style',
                'code' => 'style',
                'display_type' => 'dropdown',
                'is_visible' => true,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 4,
            ],
        ];

        $typeIds = [];
        foreach ($attributeTypes as $type) {
            $typeIds[$type['code']] = DB::table('product_attribute_types')->insertGetId(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create attribute values for Size
        $sizeValues = [
            ['value' => 'XS', 'label' => 'Extra Small', 'sort_order' => 1],
            ['value' => 'S', 'label' => 'Small', 'sort_order' => 2],
            ['value' => 'M', 'label' => 'Medium', 'sort_order' => 3],
            ['value' => 'L', 'label' => 'Large', 'sort_order' => 4],
            ['value' => 'XL', 'label' => 'Extra Large', 'sort_order' => 5],
            ['value' => '2XL', 'label' => '2X Large', 'sort_order' => 6],
            ['value' => '3XL', 'label' => '3X Large', 'sort_order' => 7],
        ];

        foreach ($sizeValues as $value) {
            DB::table('product_attribute_values')->insert(array_merge($value, [
                'attribute_type_id' => $typeIds['size'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create attribute values for Color
        $colorValues = [
            ['value' => 'Black', 'label' => 'Black', 'swatch_value' => '#000000', 'sort_order' => 1],
            ['value' => 'White', 'label' => 'White', 'swatch_value' => '#FFFFFF', 'sort_order' => 2],
            ['value' => 'Navy', 'label' => 'Navy Blue', 'swatch_value' => '#000080', 'sort_order' => 3],
            ['value' => 'Red', 'label' => 'Red', 'swatch_value' => '#FF0000', 'sort_order' => 4],
            ['value' => 'Green', 'label' => 'Green', 'swatch_value' => '#008000', 'sort_order' => 5],
            ['value' => 'Blue', 'label' => 'Blue', 'swatch_value' => '#0000FF', 'sort_order' => 6],
            ['value' => 'Gray', 'label' => 'Gray', 'swatch_value' => '#808080', 'sort_order' => 7],
            ['value' => 'Brown', 'label' => 'Brown', 'swatch_value' => '#8B4513', 'sort_order' => 8],
            ['value' => 'Beige', 'label' => 'Beige', 'swatch_value' => '#F5F5DC', 'sort_order' => 9],
            ['value' => 'Pink', 'label' => 'Pink', 'swatch_value' => '#FFC0CB', 'sort_order' => 10],
        ];

        foreach ($colorValues as $value) {
            DB::table('product_attribute_values')->insert(array_merge($value, [
                'attribute_type_id' => $typeIds['color'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create attribute values for Material
        $materialValues = [
            ['value' => 'Cotton', 'label' => 'Cotton', 'sort_order' => 1],
            ['value' => 'Polyester', 'label' => 'Polyester', 'sort_order' => 2],
            ['value' => 'Leather', 'label' => 'Leather', 'sort_order' => 3],
            ['value' => 'Wool', 'label' => 'Wool', 'sort_order' => 4],
            ['value' => 'Silk', 'label' => 'Silk', 'sort_order' => 5],
            ['value' => 'Denim', 'label' => 'Denim', 'sort_order' => 6],
            ['value' => 'Linen', 'label' => 'Linen', 'sort_order' => 7],
        ];

        foreach ($materialValues as $value) {
            DB::table('product_attribute_values')->insert(array_merge($value, [
                'attribute_type_id' => $typeIds['material'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create attribute values for Style
        $styleValues = [
            ['value' => 'Casual', 'label' => 'Casual', 'sort_order' => 1],
            ['value' => 'Formal', 'label' => 'Formal', 'sort_order' => 2],
            ['value' => 'Sport', 'label' => 'Sport', 'sort_order' => 3],
            ['value' => 'Vintage', 'label' => 'Vintage', 'sort_order' => 4],
            ['value' => 'Modern', 'label' => 'Modern', 'sort_order' => 5],
        ];

        foreach ($styleValues as $value) {
            DB::table('product_attribute_values')->insert(array_merge($value, [
                'attribute_type_id' => $typeIds['style'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
