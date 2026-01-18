<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Dropshipper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupplierDropshipperSeeder extends Seeder
{
    /**
     * Seed suppliers and dropshippers from the contact list CSV.
     */
    public function run(): void
    {
        $this->seedDropshippers();
        $this->seedSuppliers();
    }

    /**
     * Seed dropshippers (companies with dropship capabilities).
     */
    private function seedDropshippers(): void
    {
        $dropshippers = [
            [
                'company_name' => 'TopDawg',
                'contact_name' => 'Support Team',
                'email' => 'support@topdawg.com',
                'phone' => '(954) 251-3176',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: US / Multi-niche\nType: Dropship / Hybrid\nAPI/Tech: Full API + apps\nBest For: Western boots, outdoor items\nPrivate Label: No\nWebsite: topdawg.com",
            ],
            [
                'company_name' => 'BrandsGateway',
                'contact_name' => 'Support Team',
                'email' => 'support@brandsgateway.com',
                'phone' => '+46 36100129',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: EU Fashion\nType: Dropship\nAPI/Tech: API + integrations\nBest For: Designer Western-style apparel\nPrivate Label: No\nWebsite: brandsgateway.com",
            ],
            [
                'company_name' => 'Griffati',
                'contact_name' => 'Support Desk',
                'email' => 'desk@griffati.com',
                'phone' => '+39 049 825 8813',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: EU Fashion\nType: Dropship / Wholesaler\nAPI/Tech: Dropship API\nBest For: Western-inspired fashion\nPrivate Label: No\nWebsite: griffati.com",
            ],
            [
                'company_name' => 'All Seasons Clothing Co.',
                'contact_name' => 'Website Contact',
                'email' => 'info@allseasonsclothingcompany.com',
                'phone' => '907-357-0123',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: US Footwear\nType: Wholesaler / Dropship\nAPI/Tech: Manual + integrators\nBest For: Western boots\nPrivate Label: No\nWebsite: allseasonsclothingcompany.com",
            ],
            [
                'company_name' => 'Dropshipzone AU',
                'contact_name' => 'Info Team',
                'email' => 'info@dropshipzone.com.au',
                'phone' => '+61 3 9376 0841',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Australia\nType: Dropshipper\nAPI/Tech: API + Shopify\nBest For: AU apparel & accessories\nPrivate Label: No\nWebsite: dropshipzone.com.au",
            ],
            [
                'company_name' => 'EPROLO',
                'contact_name' => 'Support Team',
                'email' => 'support@eprolo.com',
                'phone' => '(281) 694-5687',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Global / AU Warehouses\nType: Dropship / Hybrid\nAPI/Tech: Full API\nBest For: Apparel + outdoor gear\nPrivate Label: Yes - Full PL\nWebsite: eprolo.com",
            ],
            [
                'company_name' => 'Seasonsway',
                'contact_name' => 'Support Team',
                'email' => 'support@seasonsway.com',
                'phone' => '+91 98703-24320',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Australia\nType: Dropshipper\nAPI/Tech: Shopify / Amazon\nBest For: AU apparel\nPrivate Label: No\nWebsite: seasonsway.com",
            ],
            [
                'company_name' => 'Wefulfil',
                'contact_name' => 'Hello Team',
                'email' => 'hello@wefulfil.com',
                'phone' => 'Via website',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Australia\nType: Dropship / 3PL\nAPI/Tech: Platform integrations\nBest For: Boutique AU apparel\nPrivate Label: Partial\nWebsite: wefulfil.com.au",
            ],
            [
                'company_name' => 'Survival Frog',
                'contact_name' => 'Support Team',
                'email' => 'support@survivalfrog.com',
                'phone' => '1-800-773-7737',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: US\nType: Wholesaler / Dropship\nAPI/Tech: Feeds / integrators\nBest For: Survival kits\nPrivate Label: No\nWebsite: survivalfrog.com",
            ],
            [
                'company_name' => 'Camping Dropship',
                'contact_name' => 'Dropship Team',
                'email' => 'dropship@campingdropship.com',
                'phone' => '(760) 994-0710',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: US Outdoor\nType: Dropship Distributor\nAPI/Tech: Feeds + integrators\nBest For: Camping gear\nPrivate Label: No\nWebsite: campingdropship.com",
            ],
            [
                'company_name' => 'Wholesale Survival Club',
                'contact_name' => 'Customer Service',
                'email' => 'CustomerService@WholesaleRightNow.Net',
                'phone' => '(760) 994-0710',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: US Survival Network\nType: Wholesale + Dropship\nAPI/Tech: Product feeds\nBest For: Tactical & survival gear\nPrivate Label: No\nWebsite: wholesalesurvivalclub.com",
            ],
            [
                'company_name' => 'Doba',
                'contact_name' => 'Help Team',
                'email' => 'Help@doba.com',
                'phone' => '(801) 765-6000',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Global\nType: Dropship Platform\nAPI/Tech: Apps + API\nBest For: Survival gear + multi-niche\nPrivate Label: No\nWebsite: doba.com",
            ],
            [
                'company_name' => 'Wholesale2B',
                'contact_name' => 'Support Team',
                'email' => 'support@wholesale2b.com',
                'phone' => '855-488-5235',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Global Aggregator\nType: Dropship Platform\nAPI/Tech: Apps + API\nBest For: Multi-category\nPrivate Label: No\nWebsite: wholesale2b.com",
            ],
            [
                'company_name' => 'CJDropshipping',
                'contact_name' => 'Support Team',
                'email' => 'support@cjdropshipping.com',
                'phone' => '0571-86719839',
                'status' => 'pending',
                'commission_rate' => 5.00,
                'total_orders' => 0,
                'total_revenue' => 0,
                'notes' => "Region: Global\nType: Dropship / Fulfillment\nAPI/Tech: Full API\nBest For: Camping, survival, apparel\nPrivate Label: Yes - Branding\nWebsite: cjdropshipping.com",
            ],
        ];

        foreach ($dropshippers as $data) {
            // Generate API key and secret
            $data['api_key'] = 'ds_' . Str::random(32);
            $data['api_secret'] = hash('sha256', Str::random(64));

            // Check if already exists by email
            $existing = Dropshipper::where('email', $data['email'])->first();
            if (!$existing) {
                Dropshipper::create($data);
                $this->command->info("Created dropshipper: {$data['company_name']}");
            } else {
                $this->command->warn("Dropshipper already exists: {$data['company_name']}");
            }
        }
    }

    /**
     * Seed suppliers (wholesalers, manufacturers, distributors).
     */
    private function seedSuppliers(): void
    {
        $suppliers = [
            [
                'company_name' => 'Wholesale Accessory Market',
                'contact_name' => 'Customer Service',
                'email' => 'customerservice@ewam.com',
                'phone' => '877-524-0433',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Western Accessories\nType: Wholesaler\nAPI/Tech: No public API\nBest For: Western jewelry & gifts\nPrivate Label: Partial\nWebsite: wholesaleaccessorymarket.com",
            ],
            [
                'company_name' => 'Katydid Wholesale',
                'contact_name' => 'Info Team',
                'email' => 'info@katydidwholesale.com',
                'phone' => '469-324-6254',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Western Boutique\nType: Wholesaler\nAPI/Tech: Integrator-based\nBest For: Western chic apparel\nPrivate Label: Yes\nWebsite: katydidwholesale.com",
            ],
            [
                'company_name' => 'Western Express',
                'contact_name' => 'Website Contact',
                'email' => 'sales@wexpress.com',
                'phone' => 'Via website',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Western Accessories\nType: Distributor\nAPI/Tech: Feeds via integrators\nBest For: Hats, belts, bolo ties\nPrivate Label: No\nWebsite: wexpress.com",
            ],
            [
                'company_name' => 'Kakadu Traders Australia',
                'contact_name' => 'Sales Team',
                'email' => 'sales@kakadutraders.com.au',
                'phone' => '+61 2 9709 3555',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Australia\nType: Wholesaler\nAPI/Tech: B2B\nBest For: Oilskins & AU workwear\nPrivate Label: No\nWebsite: kakaduaustralia.com",
            ],
            [
                'company_name' => 'Ringers Western',
                'contact_name' => 'Info Team',
                'email' => 'info@ringerswestern.com',
                'phone' => '07 5606 0748',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Australia\nType: Brand Wholesaler\nAPI/Tech: B2B\nBest For: AU Western-style apparel\nPrivate Label: No\nWebsite: ringerswestern.com",
            ],
            [
                'company_name' => 'Circle L Australia',
                'contact_name' => 'Website Contact',
                'email' => 'sales@circlel.com.au',
                'phone' => 'Via website',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Australia\nType: Brand Wholesaler\nAPI/Tech: B2B\nBest For: Western apparel, hats, saddlery\nPrivate Label: No\nWebsite: circlel.com.au",
            ],
            [
                'company_name' => 'Mike Williams Country',
                'contact_name' => 'Website Contact',
                'email' => 'info@mikewilliamscountry.com.au',
                'phone' => '+61 7 4639 1510',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Australia\nType: Retail/Wholesale Hybrid\nAPI/Tech: B2B\nBest For: Multi-brand Western wear\nPrivate Label: No\nWebsite: mikewilliamscountry.com.au",
            ],
            [
                'company_name' => 'Inventory Source',
                'contact_name' => 'Support Team',
                'email' => 'support@inventorysource.com',
                'phone' => '+1 888 351 3497',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Global\nType: Integrator Platform\nAPI/Tech: Automation platform\nBest For: Multi-supplier feeds\nPrivate Label: No (platform)\nWebsite: inventorysource.com\n\nNote: This is an integration platform, not a direct supplier.",
            ],
            [
                'company_name' => 'Flxpoint',
                'contact_name' => 'Support Portal',
                'email' => 'support@flxpoint.com',
                'phone' => 'Via website',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Global\nType: Integrator Platform\nAPI/Tech: API + deep integrations\nBest For: Tactical / survival distributors\nPrivate Label: No (platform)\nWebsite: flxpoint.com\n\nNote: This is an integration platform, not a direct supplier.",
            ],
            [
                'company_name' => 'Spark Shipping',
                'contact_name' => 'Contact Team',
                'email' => 'contact@sparkshipping.com',
                'phone' => '(617) 934-6559',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Global\nType: Automation Platform\nAPI/Tech: API-based\nBest For: Outdoor/survival automation\nPrivate Label: No (platform)\nWebsite: sparkshipping.com\n\nNote: This is an automation platform, not a direct supplier.",
            ],
            [
                'company_name' => 'Zanders',
                'contact_name' => 'Info Team',
                'email' => 'info@gzanders.com',
                'phone' => '800-851-4373',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Outdoor\nType: Wholesale Distributor\nAPI/Tech: Integrators\nBest For: Hunting/survival gear\nPrivate Label: No\nWebsite: gzanders.com",
            ],
            [
                'company_name' => 'Worldwide Brands',
                'contact_name' => 'Info Team',
                'email' => 'info@worldwidebrands.com',
                'phone' => '1-877-376-7747',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Global Directory\nType: Directory\nAPI/Tech: Member portal\nBest For: Vetted suppliers\nPrivate Label: No (directory)\nWebsite: worldwidebrands.com\n\nNote: This is a supplier directory, not a direct supplier.",
            ],
            [
                'company_name' => 'Jacks Manufacturing',
                'contact_name' => 'Info Team',
                'email' => 'info@jacksmfg.com',
                'phone' => '877-335-5121',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Western/Ranch\nType: Manufacturer / Wholesaler\nAPI/Tech: No API\nBest For: Western tack, ranch gear\nPrivate Label: Yes - Custom\nWebsite: jacksmfg.com",
            ],
            [
                'company_name' => 'Oceas Outdoor Gear',
                'contact_name' => 'Website Contact',
                'email' => 'info@oceasoutdoors.com',
                'phone' => 'Via website',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Outdoor/Survival\nType: Manufacturer / Wholesaler\nAPI/Tech: No API\nBest For: Survival blankets, tarps\nPrivate Label: Yes - White Label\nWebsite: oceasoutdoors.com",
            ],
            [
                'company_name' => 'Exxel Outdoors',
                'contact_name' => 'Outdoor Team',
                'email' => 'outdoor@exxel.com',
                'phone' => '(800) 479-5927',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Outdoor\nType: Manufacturer\nAPI/Tech: No API\nBest For: Sleeping bags, tents, survival\nPrivate Label: Yes - Full PL\nWebsite: exxel.com",
            ],
            [
                'company_name' => 'Rocky Mountain Survival Gear',
                'contact_name' => 'TLC Team',
                'email' => 'tlc@rmsgear.com',
                'phone' => '877-843-5559',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: US Survival\nType: Manufacturer/Wholesaler\nAPI/Tech: No API\nBest For: Survival kits & tools\nPrivate Label: Yes - Small MOQ PL\nWebsite: rmsgear.com",
            ],
            [
                'company_name' => 'The Print Bar (AU)',
                'contact_name' => 'Hello Team',
                'email' => 'hello@theprintbar.com',
                'phone' => '07 3854 0608',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Australia Apparel\nType: Manufacturer / POD\nAPI/Tech: Shopify apps\nBest For: Western/AU apparel\nPrivate Label: Yes - Private Label\nWebsite: theprintbar.com",
            ],
            [
                'company_name' => 'Private Label Apparel AU',
                'contact_name' => 'Accounts Team',
                'email' => 'accounts@australianprivatelabel.com.au',
                'phone' => '02 8311 7466',
                'status' => 'pending',
                'payment_terms' => 'Net 30',
                'total_orders' => 0,
                'total_amount' => 0,
                'notes' => "Region: Australia Apparel\nType: Manufacturer\nAPI/Tech: No API\nBest For: Full custom apparel lines\nPrivate Label: Yes - Full PL\nWebsite: australianprivatelabel.com.au",
            ],
        ];

        foreach ($suppliers as $data) {
            // Check if already exists by email
            $existing = Supplier::where('email', $data['email'])->first();
            if (!$existing) {
                Supplier::create($data);
                $this->command->info("Created supplier: {$data['company_name']}");
            } else {
                $this->command->warn("Supplier already exists: {$data['company_name']}");
            }
        }
    }
}
