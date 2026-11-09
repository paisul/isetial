<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jersey_products', function (Blueprint $table) {
            $table->string('model')->nullable()->after('name');
            $table->string('sleeve', 10)->nullable()->after('model');
            $table->string('image_path')->nullable()->after('description');
        });

        $variants = [
            'Lelaki Pendek' => ['sleeve' => 'short', 'price' => 199, 'image' => 'images/jersey/model-pria-pendek-v2.jpg'],
            'Lelaki Panjang' => ['sleeve' => 'long', 'price' => 249, 'image' => 'images/jersey/model-pria-panjang-v3.jpg'],
            'Muslimah' => ['sleeve' => 'long', 'price' => 299, 'image' => 'images/jersey/model-muslimah-v3.jpg'],
        ];

        DB::transaction(function () use ($variants) {
            DB::table('jersey_products')->whereNull('model')->orderBy('id')->get()->each(function ($legacy) use ($variants) {
                $legacySizes = DB::table('jersey_sizes')->where('jersey_product_id', $legacy->id)->orderBy('id')->get();
                $variantProductIds = [];

                foreach ($variants as $model => $variant) {
                    $name = str_contains(strtolower($legacy->name), 'isetial') ? $model : "{$legacy->name} - {$model}";
                    if ($model === 'Lelaki Pendek') {
                        DB::table('jersey_products')->where('id', $legacy->id)->update([
                            'name' => $name, 'model' => $model, 'sleeve' => $variant['sleeve'],
                            'price' => $variant['price'], 'image_path' => $variant['image'], 'updated_at' => now(),
                        ]);
                        $variantProductIds[$model] = $legacy->id;
                        foreach ($legacySizes as $size) {
                            DB::table('jersey_sizes')->where('id', $size->id)->update(['price_adjustment' => in_array($size->name, ['3XL', '4XL', '5XL'], true) ? 20 : $size->price_adjustment]);
                        }
                        continue;
                    }

                    $productId = DB::table('jersey_products')->insertGetId([
                        'name' => $name, 'model' => $model, 'sleeve' => $variant['sleeve'], 'description' => $legacy->description,
                        'image_path' => $variant['image'], 'price' => $variant['price'], 'is_active' => $legacy->is_active,
                        'created_at' => $legacy->created_at, 'updated_at' => now(), 'deleted_at' => $legacy->deleted_at,
                    ]);
                    $variantProductIds[$model] = $productId;
                    foreach ($legacySizes as $size) {
                        DB::table('jersey_sizes')->insert([
                            'jersey_product_id' => $productId, 'name' => $size->name,
                            'price_adjustment' => in_array($size->name, ['3XL', '4XL', '5XL'], true) ? 20 : $size->price_adjustment,
                            'stock' => $size->stock, 'created_at' => $size->created_at, 'updated_at' => now(),
                        ]);
                    }
                }

                foreach (['Lelaki Panjang', 'Muslimah'] as $model) {
                    $newProductId = $variantProductIds[$model];
                    DB::table('jersey_order_items')->where('jersey_product_id', $legacy->id)->where('model', $model)->get()->each(function ($item) use ($legacySizes, $newProductId) {
                        $oldSize = $legacySizes->firstWhere('id', $item->jersey_size_id);
                        $newSizeId = $oldSize ? DB::table('jersey_sizes')->where('jersey_product_id', $newProductId)->where('name', $oldSize->name)->value('id') : null;
                        if ($newSizeId) DB::table('jersey_order_items')->where('id', $item->id)->update(['jersey_product_id' => $newProductId, 'jersey_size_id' => $newSizeId]);
                    });
                }
            });
        });
    }

    public function down(): void
    {
        Schema::table('jersey_products', function (Blueprint $table) {
            $table->dropColumn(['model', 'sleeve', 'image_path']);
        });
    }
};
