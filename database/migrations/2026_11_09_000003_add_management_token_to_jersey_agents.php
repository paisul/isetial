<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jersey_agents', function (Blueprint $table) {
            $table->string('management_token', 64)->nullable()->unique()->after('token');
        });

        DB::table('jersey_agents')->orderBy('id')->get(['id'])->each(function ($agent) {
            DB::table('jersey_agents')->where('id', $agent->id)->update(['management_token' => bin2hex(random_bytes(24))]);
        });
    }

    public function down(): void
    {
        Schema::table('jersey_agents', function (Blueprint $table) {
            $table->dropUnique(['management_token']);
            $table->dropColumn('management_token');
        });
    }
};
