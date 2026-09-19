<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->updateNames([
            'darulhikmah' => 'Darulhikmah',
            'jamihakim' => "Jami' Darulhakim",
            'fatimah' => 'Al-fatonah',
            'naim' => 'Jannatussalam',
            'aman' => 'Darulaman',
        ]);
    }

    public function down(): void
    {
        $this->updateNames([
            'darulhikmah' => 'Darul Hikmah',
            'jamihakim' => 'Jami Hakim',
            'fatimah' => 'Fatimah',
            'naim' => 'Naim',
            'aman' => 'Aman',
        ]);
    }

    private function updateNames(array $masjids): void
    {
        foreach ($masjids as $slug => $name) {
            DB::table('masjids')->where('slug', $slug)->update([
                'name' => $name,
                'updated_at' => now(),
            ]);
        }
    }
};
