<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->unique(['person_id', 'organization_period_id'], 'position_assignments_person_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropUnique('position_assignments_person_period_unique');
        });
    }
};
