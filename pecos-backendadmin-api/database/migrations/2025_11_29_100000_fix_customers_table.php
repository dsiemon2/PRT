<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix the legacy customers table to have a proper auto-increment primary key
     */
    public function up(): void
    {
        // First, check if ID column needs to be fixed
        $columns = DB::select("SHOW COLUMNS FROM customers WHERE Field = 'ID'");
        $idColumn = $columns[0] ?? null;

        if ($idColumn) {
            // Check if ID has values
            $hasIds = DB::table('customers')->whereNotNull('ID')->where('ID', '>', 0)->exists();

            if (!$hasIds) {
                // Drop the existing ID column and recreate as auto-increment primary key
                Schema::table('customers', function (Blueprint $table) {
                    $table->dropColumn('ID');
                });

                // Add new auto-increment ID as primary key
                DB::statement('ALTER TABLE customers ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST');
            } else {
                // Just make existing ID the primary key if it has values
                DB::statement('ALTER TABLE customers MODIFY ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
            }
        }
    }

    public function down(): void
    {
        // Revert to original structure would be complex - skip for now
    }
};
