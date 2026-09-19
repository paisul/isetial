<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_periods', function (Blueprint $table) {
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft')->after('ends_at');
        });
        DB::table('organization_periods')->where('is_active', true)->update(['status' => 'active']);
        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('masjid_id')->constrained('positions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('positions', fn (Blueprint $table) => $table->dropConstrainedForeignId('parent_id'));
        Schema::table('organization_periods', fn (Blueprint $table) => $table->dropColumn('status'));
    }
};
