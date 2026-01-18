<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtyProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing specialty data (disable foreign key checks first)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('specialty_products')->truncate();
        DB::table('specialty_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Define 8 specialty categories
        $categories = [
            [
                'category_id' => 100,
                'label' => 'Oilskin Jackets & Coats',
                'description' => 'Premium waxed canvas outerwear built for the toughest conditions. Water-resistant and durable for outdoor work and adventure.',
                'image' => 'images/categories/oilskin-jackets.svg',
                'sort_order' => 1,
            ],
            [
                'category_id' => 101,
                'label' => 'Oilskin Vests',
                'description' => 'Versatile weather-resistant vests with ample storage. Perfect layering piece for variable conditions.',
                'image' => 'images/categories/oilskin-vests.svg',
                'sort_order' => 2,
            ],
            [
                'category_id' => 102,
                'label' => 'Military T-Shirts',
                'description' => 'Honor military service with authentic insignia apparel. Quality cotton shirts featuring official military branch designs.',
                'image' => 'images/categories/military-tshirts.svg',
                'sort_order' => 3,
            ],
            [
                'category_id' => 103,
                'label' => 'Short Stories',
                'description' => 'Entertaining fiction and tales based on real-life adventures. Digital downloads available instantly.',
                'image' => 'images/categories/short-stories.svg',
                'sort_order' => 4,
            ],
            [
                'category_id' => 104,
                'label' => 'Moccasins',
                'description' => 'Handcrafted genuine leather moccasins for indoor and outdoor comfort. Traditional Native American styling.',
                'image' => 'images/categories/moccasins.svg',
                'sort_order' => 5,
            ],
            [
                'category_id' => 105,
                'label' => 'Diabetic Footwear',
                'description' => 'Specially designed shoes for diabetic foot care. Extra depth, seamless interiors, and therapeutic comfort.',
                'image' => 'images/categories/diabetic-footwear.svg',
                'sort_order' => 6,
            ],
            [
                'category_id' => 106,
                'label' => 'Concho Belts',
                'description' => 'Authentic Western concho belts with sterling silver and turquoise accents. Handcrafted artisan quality.',
                'image' => 'images/categories/concho-belts.svg',
                'sort_order' => 7,
            ],
            [
                'category_id' => 107,
                'label' => 'Western Accessories',
                'description' => 'Complete your Western look with buckles, bolo ties, and hat bands. Quality craftsmanship in every piece.',
                'image' => 'images/categories/western-accessories.svg',
                'sort_order' => 8,
            ],
        ];

        // Insert categories
        foreach ($categories as $cat) {
            DB::table('specialty_categories')->insert([
                'category_id' => $cat['category_id'],
                'label' => $cat['label'],
                'description' => $cat['description'],
                'image' => $cat['image'],
                'sort_order' => $cat['sort_order'],
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get inserted category IDs
        $specialtyCategories = DB::table('specialty_categories')->get();

        foreach ($specialtyCategories as $specCat) {
            // Try to get existing products from products3 for this category
            $existingProducts = DB::table('products3')
                ->where('CategoryCode', $specCat->category_id)
                ->limit(4)
                ->get();

            if ($existingProducts->count() > 0) {
                // Use existing products
                $sortOrder = 1;
                foreach ($existingProducts as $product) {
                    // Ensure product has stock
                    DB::table('products3')
                        ->where('UPC', $product->UPC)
                        ->update(['QTY' => 100]);

                    // Determine sizes and colors based on category
                    $sizes = $this->getSizesForCategory($specCat->category_id, $product);
                    $colors = $this->getColorsForCategory($specCat->category_id, $product);

                    DB::table('specialty_products')->insert([
                        'specialty_category_id' => $specCat->id,
                        'upc' => $product->UPC,
                        'label' => $product->ShortDescription ?? 'Product',
                        'description' => $product->LngDescription ?? '',
                        'sizes' => $sizes,
                        'colors' => $colors,
                        'price' => null, // Use product's original price
                        'sort_order' => $sortOrder++,
                        'is_visible' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Create sample products for new categories
                $sampleProducts = $this->getSampleProductsForCategory($specCat->category_id);
                $sortOrder = 1;

                foreach ($sampleProducts as $sample) {
                    DB::table('specialty_products')->insert([
                        'specialty_category_id' => $specCat->id,
                        'upc' => null, // No linked product
                        'label' => $sample['label'],
                        'description' => $sample['description'],
                        'sizes' => $sample['sizes'],
                        'colors' => $sample['colors'],
                        'price' => $sample['price'],
                        'sort_order' => $sortOrder++,
                        'is_visible' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Get sizes based on category and product
     */
    private function getSizesForCategory($categoryId, $product): string
    {
        // Use product's ItemSize if available
        if (!empty($product->ItemSize)) {
            return $product->ItemSize;
        }

        // Default sizes by category
        switch ($categoryId) {
            case 100: // Jackets
            case 101: // Vests
                return 'S,M,L,XL,2XL,3XL';
            case 102: // T-Shirts
                return 'S,M,L,XL,2XL';
            case 103: // Short Stories
                return 'Digital';
            default:
                return '';
        }
    }

    /**
     * Get colors based on category and product
     */
    private function getColorsForCategory($categoryId, $product): string
    {
        switch ($categoryId) {
            case 100: // Jackets
                return 'Tobacco,Mustard';
            case 101: // Vests
                return 'Brown,Black,Tobacco';
            case 102: // T-Shirts
                return 'Black,Navy,Olive';
            case 103: // Short Stories
                return '';
            default:
                return '';
        }
    }

    /**
     * Get sample products for new categories
     */
    private function getSampleProductsForCategory($categoryId): array
    {
        switch ($categoryId) {
            case 104: // Moccasins
                return [
                    [
                        'label' => 'Classic Leather Moccasin',
                        'description' => 'Traditional soft-sole moccasin crafted from premium deer hide. Hand-stitched with genuine leather laces.',
                        'sizes' => '7,8,9,10,11,12,13',
                        'colors' => 'Brown,Tan,Black',
                        'price' => 79.99,
                    ],
                    [
                        'label' => 'Fleece-Lined Moccasin',
                        'description' => 'Cozy indoor moccasin with genuine sheepskin fleece lining. Soft suede exterior with rubber sole.',
                        'sizes' => '7,8,9,10,11,12,13',
                        'colors' => 'Chestnut,Grey,Navy',
                        'price' => 89.99,
                    ],
                    [
                        'label' => 'Beaded Moccasin',
                        'description' => 'Authentic Native American design with hand-sewn beadwork. Genuine elk hide construction.',
                        'sizes' => '7,8,9,10,11,12',
                        'colors' => 'Natural,Brown',
                        'price' => 129.99,
                    ],
                    [
                        'label' => 'Driving Moccasin',
                        'description' => 'Modern moccasin with rubber driving sole. Premium cowhide leather with cushioned insole.',
                        'sizes' => '8,9,10,11,12,13',
                        'colors' => 'Cognac,Black,Navy',
                        'price' => 99.99,
                    ],
                ];

            case 105: // Diabetic Footwear
                return [
                    [
                        'label' => 'Therapeutic Walking Shoe',
                        'description' => 'Extra-depth design with removable insoles for custom orthotics. Seamless interior prevents irritation.',
                        'sizes' => '7,8,9,10,11,12,13,14',
                        'colors' => 'Black,White,Grey',
                        'price' => 149.99,
                    ],
                    [
                        'label' => 'Diabetic Comfort Sandal',
                        'description' => 'Open-toe design with adjustable straps for swelling. Cushioned footbed with arch support.',
                        'sizes' => '7,8,9,10,11,12,13',
                        'colors' => 'Brown,Black',
                        'price' => 89.99,
                    ],
                    [
                        'label' => 'Stretch Knit Diabetic Shoe',
                        'description' => 'Breathable stretch upper accommodates foot changes. Lightweight with shock-absorbing sole.',
                        'sizes' => '7,8,9,10,11,12,13,14',
                        'colors' => 'Black,Navy,Burgundy',
                        'price' => 119.99,
                    ],
                    [
                        'label' => 'Diabetic Boot',
                        'description' => 'Ankle-height boot with extra depth and wide toe box. Padded collar and tongue for comfort.',
                        'sizes' => '8,9,10,11,12,13,14',
                        'colors' => 'Black,Brown',
                        'price' => 179.99,
                    ],
                ];

            case 106: // Concho Belts
                return [
                    [
                        'label' => 'Sterling Silver Concho Belt',
                        'description' => 'Hand-stamped sterling silver conchos on genuine leather. Traditional Navajo butterfly design.',
                        'sizes' => '32,34,36,38,40,42,44',
                        'colors' => 'Brown,Black',
                        'price' => 299.99,
                    ],
                    [
                        'label' => 'Turquoise Concho Belt',
                        'description' => 'Genuine turquoise stones set in silver-plated conchos. Southwestern style on tooled leather.',
                        'sizes' => '32,34,36,38,40,42',
                        'colors' => 'Tan,Brown',
                        'price' => 249.99,
                    ],
                    [
                        'label' => 'Copper Concho Belt',
                        'description' => 'Antiqued copper conchos with Celtic knot design. Heavy-duty leather strap.',
                        'sizes' => '32,34,36,38,40,42,44',
                        'colors' => 'Black,Dark Brown',
                        'price' => 159.99,
                    ],
                    [
                        'label' => 'Western Link Concho Belt',
                        'description' => 'Linked silver-tone conchos create a flexible fit. Decorative ranger buckle included.',
                        'sizes' => 'S/M,L/XL',
                        'colors' => 'Brown,Black,Tan',
                        'price' => 129.99,
                    ],
                ];

            case 107: // Western Accessories
                return [
                    [
                        'label' => 'Sterling Silver Bolo Tie',
                        'description' => 'Handcrafted sterling silver slide with braided leather cord. Turquoise and coral inlay.',
                        'sizes' => 'One Size',
                        'colors' => 'Black,Brown',
                        'price' => 149.99,
                    ],
                    [
                        'label' => 'Western Belt Buckle',
                        'description' => 'Solid brass buckle with silver overlay. Longhorn design with rope border.',
                        'sizes' => 'Standard',
                        'colors' => 'Silver,Gold,Antique',
                        'price' => 79.99,
                    ],
                    [
                        'label' => 'Leather Hat Band',
                        'description' => 'Genuine leather hat band with silver conchos. Fits most cowboy hat styles.',
                        'sizes' => 'S,M,L,XL',
                        'colors' => 'Brown,Black,Tan',
                        'price' => 49.99,
                    ],
                    [
                        'label' => 'Horsehair Bracelet',
                        'description' => 'Braided horsehair bracelet with silver clasp. Each piece unique in pattern.',
                        'sizes' => '7,7.5,8,8.5',
                        'colors' => 'Natural,Black,Sorrel',
                        'price' => 59.99,
                    ],
                ];

            default:
                return [];
        }
    }
}
