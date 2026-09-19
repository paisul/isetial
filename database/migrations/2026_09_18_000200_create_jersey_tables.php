<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jersey_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('jersey_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jersey_product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 20);
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['jersey_product_id', 'name']);
        });
        Schema::create('jersey_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->date('birth_date')->nullable();
            $table->text('address');
            $table->string('phone', 30)->index();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('production_status', ['queued', 'processing', 'ready', 'delivered', 'cancelled'])->default('queued');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('jersey_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jersey_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jersey_product_id')->constrained()->restrictOnDelete();
            $table->foreignId('jersey_size_id')->constrained()->restrictOnDelete();
            $table->string('model');
            $table->enum('sleeve', ['short', 'long']);
            $table->unsignedTinyInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
        Schema::create('jersey_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jersey_order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('proof_path');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['jersey_payments', 'jersey_order_items', 'jersey_orders', 'jersey_sizes', 'jersey_products'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
