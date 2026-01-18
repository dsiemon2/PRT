<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnReasonSeeder extends Seeder
{
    /**
     * Seed the return reasons table.
     */
    public function run(): void
    {
        $reasons = [
            [
                'name' => 'Damaged in Transit',
                'code' => 'DAMAGED_TRANSIT',
                'description' => 'Item was damaged during shipping',
                'requires_photo' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Defective Product',
                'code' => 'DEFECTIVE',
                'description' => 'Product is not working properly or has a manufacturing defect',
                'requires_photo' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Wrong Item Received',
                'code' => 'WRONG_ITEM',
                'description' => 'Received a different item than what was ordered',
                'requires_photo' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Item Not as Described',
                'code' => 'NOT_AS_DESCRIBED',
                'description' => 'Product does not match the description or images on the website',
                'requires_photo' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Changed Mind',
                'code' => 'CHANGED_MIND',
                'description' => 'Customer no longer wants the item',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Better Price Found',
                'code' => 'BETTER_PRICE',
                'description' => 'Customer found a better price elsewhere',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Ordered Wrong Size/Color',
                'code' => 'WRONG_SIZE_COLOR',
                'description' => 'Customer ordered the wrong size or color',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Product Did Not Meet Expectations',
                'code' => 'EXPECTATIONS',
                'description' => 'Product did not meet customer expectations',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Duplicate Order',
                'code' => 'DUPLICATE',
                'description' => 'Customer accidentally placed the same order twice',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Missing Parts/Accessories',
                'code' => 'MISSING_PARTS',
                'description' => 'Product is missing parts or accessories',
                'requires_photo' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Quality Not as Expected',
                'code' => 'QUALITY_ISSUE',
                'description' => 'Product quality is lower than expected',
                'requires_photo' => true,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Arrived Too Late',
                'code' => 'LATE_ARRIVAL',
                'description' => 'Product arrived after the needed date',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Other',
                'code' => 'OTHER',
                'description' => 'Other reason not listed above',
                'requires_photo' => false,
                'is_active' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($reasons as $reason) {
            DB::table('return_reasons')->updateOrInsert(
                ['code' => $reason['code']],
                array_merge($reason, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
