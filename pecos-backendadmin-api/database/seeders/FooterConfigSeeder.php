<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterConfigSeeder extends Seeder
{
    /**
     * Seed the footer configuration to match current PRT3 footer.php structure.
     */
    public function run(): void
    {
        // Create footer columns
        $columns = [
            ['id' => 1, 'title' => 'Shop', 'position' => 1, 'is_visible' => true, 'column_type' => 'links'],
            ['id' => 2, 'title' => 'Resources', 'position' => 2, 'is_visible' => true, 'column_type' => 'links'],
            ['id' => 3, 'title' => 'Customer Service', 'position' => 3, 'is_visible' => true, 'column_type' => 'links'],
            ['id' => 4, 'title' => 'Newsletter Signup', 'position' => 4, 'is_visible' => true, 'column_type' => 'newsletter'],
        ];

        foreach ($columns as $column) {
            DB::table('footer_columns')->updateOrInsert(
                ['position' => $column['position']],
                [
                    'title' => $column['title'],
                    'is_visible' => $column['is_visible'],
                    'column_type' => $column['column_type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Get column IDs (in case they differ from insert order)
        $shopColumnId = DB::table('footer_columns')->where('position', 1)->value('id');
        $resourcesColumnId = DB::table('footer_columns')->where('position', 2)->value('id');
        $customerServiceColumnId = DB::table('footer_columns')->where('position', 3)->value('id');

        // Shop column links
        $shopLinks = [
            ['label' => 'Home', 'url' => 'index.php', 'sort_order' => 1, 'is_core' => true, 'feature_flag' => null],
            ['label' => 'All Products', 'url' => 'products/products.php', 'sort_order' => 2, 'is_core' => true, 'feature_flag' => null],
            ['label' => 'Special Products', 'url' => 'products/special-products.php', 'sort_order' => 3, 'is_core' => false, 'feature_flag' => 'specialty_products'],
            ['label' => 'Product List', 'url' => 'products/inventory.php', 'sort_order' => 4, 'is_core' => false, 'feature_flag' => null],
            ['label' => 'Shopping Cart', 'url' => 'cart/cart.php', 'sort_order' => 5, 'is_core' => true, 'feature_flag' => null],
        ];

        foreach ($shopLinks as $link) {
            DB::table('footer_links')->updateOrInsert(
                ['column_id' => $shopColumnId, 'label' => $link['label']],
                [
                    'url' => $link['url'],
                    'icon' => 'bi-chevron-right',
                    'feature_flag' => $link['feature_flag'],
                    'link_type' => 'internal',
                    'sort_order' => $link['sort_order'],
                    'is_visible' => true,
                    'is_core' => $link['is_core'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Resources column links
        $resourcesLinks = [
            ['label' => 'Blog', 'url' => 'blog/index.php', 'sort_order' => 1, 'is_core' => false, 'feature_flag' => 'blog'],
            ['label' => 'Events', 'url' => 'pages/events.php', 'sort_order' => 2, 'is_core' => false, 'feature_flag' => 'events'],
            ['label' => 'Sizing Guide', 'url' => 'policies/shoe-sizing-guide.php', 'sort_order' => 3, 'is_core' => false, 'feature_flag' => null],
            ['label' => 'Pecos Bill Legend', 'url' => 'pecos/pecos-bill.php', 'sort_order' => 4, 'is_core' => false, 'feature_flag' => null],
            ['label' => 'About Pecos River', 'url' => 'pecos/pecos-river.php', 'sort_order' => 5, 'is_core' => false, 'feature_flag' => null],
            ['label' => 'About Us', 'url' => 'pages/about-us.php', 'sort_order' => 6, 'is_core' => true, 'feature_flag' => null],
        ];

        foreach ($resourcesLinks as $link) {
            DB::table('footer_links')->updateOrInsert(
                ['column_id' => $resourcesColumnId, 'label' => $link['label']],
                [
                    'url' => $link['url'],
                    'icon' => 'bi-chevron-right',
                    'feature_flag' => $link['feature_flag'],
                    'link_type' => 'internal',
                    'sort_order' => $link['sort_order'],
                    'is_visible' => true,
                    'is_core' => $link['is_core'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Customer Service column links
        $customerServiceLinks = [
            ['label' => 'Contact Us', 'url' => 'pages/contact-us.php', 'sort_order' => 1, 'is_core' => true, 'feature_flag' => null],
            ['label' => 'Tell-A-Friend', 'url' => 'pages/tell-a-friend.php', 'sort_order' => 2, 'is_core' => false, 'feature_flag' => 'tell_a_friend'],
            ['label' => 'Shipping Policy', 'url' => 'policies/shipping-policy.php', 'sort_order' => 3, 'is_core' => true, 'feature_flag' => null],
            ['label' => 'Return Policy', 'url' => 'policies/return-policy.php', 'sort_order' => 4, 'is_core' => true, 'feature_flag' => null],
            ['label' => 'Privacy Policy', 'url' => 'policies/privacy-statement.php', 'sort_order' => 5, 'is_core' => true, 'feature_flag' => null],
        ];

        foreach ($customerServiceLinks as $link) {
            DB::table('footer_links')->updateOrInsert(
                ['column_id' => $customerServiceColumnId, 'label' => $link['label']],
                [
                    'url' => $link['url'],
                    'icon' => 'bi-chevron-right',
                    'feature_flag' => $link['feature_flag'],
                    'link_type' => 'internal',
                    'sort_order' => $link['sort_order'],
                    'is_visible' => true,
                    'is_core' => $link['is_core'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
