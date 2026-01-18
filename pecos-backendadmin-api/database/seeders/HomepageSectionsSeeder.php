<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing sections
        DB::table('homepage_sections')->truncate();

        // Sample promotional section
        DB::table('homepage_sections')->insert([
            [
                'title' => 'Holiday Sale - Up to 40% Off!',
                'admin_label' => 'Holiday Promotion',
                'content' => '
<div class="text-center">
    <p class="lead mb-4">
        Get ready for the holidays with our biggest sale of the year!<br>
        Quality boots and footwear at unbeatable prices.
    </p>
    <div class="row justify-content-center g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 p-3 shadow-sm">
                <i class="bi bi-percent display-4" style="color: #990000;"></i>
                <h4 class="mt-2 mb-0">40% OFF</h4>
                <small class="text-muted">Select Boots</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 p-3 shadow-sm">
                <i class="bi bi-truck display-4" style="color: #8B6C42;"></i>
                <h4 class="mt-2 mb-0">Free Ship</h4>
                <small class="text-muted">Orders $50+</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 p-3 shadow-sm">
                <i class="bi bi-box-seam display-4" style="color: #8B6C42;"></i>
                <h4 class="mt-2 mb-0">Gift Wrap</h4>
                <small class="text-muted">Available</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 p-3 shadow-sm">
                <i class="bi bi-arrow-repeat display-4" style="color: #8B6C42;"></i>
                <h4 class="mt-2 mb-0">Easy Returns</h4>
                <small class="text-muted">30 Days</small>
            </div>
        </div>
    </div>
    <a href="products/products.php" class="btn btn-lg px-5" style="background: #990000; color: white;">
        <i class="bi bi-bag-heart me-2"></i>Shop the Sale
    </a>
    <p class="mt-3 mb-0 small text-muted">
        <i class="bi bi-clock me-1"></i> Sale ends December 31st
    </p>
</div>',
                'background_style' => 'gradient',
                'background_color' => null,
                'is_visible' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
