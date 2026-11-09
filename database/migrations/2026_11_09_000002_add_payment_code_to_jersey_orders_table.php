<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jersey_orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('payment_code')->nullable()->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('jersey_orders', fn (Blueprint $table) => $table->dropColumn('payment_code'));
    }
};
