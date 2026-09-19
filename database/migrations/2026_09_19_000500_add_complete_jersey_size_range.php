<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $productIds = DB::table('jersey_products')->pluck('id');

        foreach ($productIds as $productId) {
            foreach (['SS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'] as $size) {
                DB::table('jersey_sizes')->insertOrIgnore([
                    'jersey_product_id' => $productId,
                    'name' => $size,
                    'price_adjustment' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Ukuran tidak dihapus agar data pesanan lama tetap aman.
    }
};
