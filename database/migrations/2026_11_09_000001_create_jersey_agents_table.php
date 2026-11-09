<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jersey_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->string('token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('jersey_orders', function (Blueprint $table) {
            $table->foreignId('jersey_agent_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jersey_orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('jersey_agent_id'));
        Schema::dropIfExists('jersey_agents');
    }
};
