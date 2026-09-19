<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\OrganizationPeriod;
use App\Models\Person;
use App\Models\Position;
use App\Models\PositionAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationStructure2026Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            OrganizationPeriod::whereNull('masjid_id')->update(['status' => 'archived', 'is_active' => false]);
            $period = OrganizationPeriod::updateOrCreate(
                ['masjid_id' => null, 'name' => '2026'],
                ['starts_at' => '2026-01-01', 'ends_at' => '2026-12-31', 'status' => 'active', 'is_active' => true],
            );

            $chair = $this->position('Ketua Umum', null, 1);
            $this->assign($period, $chair, 'Solahudin Awae', null, 1);
            $this->assign($period, $this->position('Setiausaha', $chair, 2), 'Ahmad Taleh', null, 2);
            $this->assign($period, $this->position('Bendahara', $chair, 3), 'Kaosar Teng', null, 3);

            $groups = [
                ['Kegiatan', [['Kegiatan', 'Sufian'], ['W1. Kegiatan', 'Arif Cheloong'], ['W2. Kegiatan', 'Hilmee']]],
                ['Ekonomi', [['Ekonomi', 'Tarmizee'], ['W1. Ekonomi', 'Ahamad Salaemae'], ['W2. Ekonomi', 'Haris']]],
                ['Bakti', [['Bakti', 'Aiman'], ['W1. Bakti', 'Wan Anas'], ['W2. Bakti', 'Anas Mamu']]],
                ['Pendidikan', [['Pendidikan', 'Paisul Leengadaye'], ['W. Pendidikan', 'Ilyas Waesani']]],
                ['Penerangan', [['Penerangan', 'Subhee'], ['W1. Penerangan', 'Muhammad'], ['W2. Penerangan', 'Muslim Dasae']]],
                ['Pengawasan', [['Pengawasan', 'Paisal Nidae'], ['W1. Pengawasan', 'Hafis Chala'], ['W2. Pengawasan', 'Ihsan Yusoh']]],
            ];

            $order = 10;
            foreach ($groups as [$divisionName, $members]) {
                $division = Division::updateOrCreate(['masjid_id' => null, 'name' => $divisionName], ['description' => null]);
                $head = null;
                foreach ($members as $index => [$role, $name]) {
                    $position = $this->position($role, $index === 0 ? $chair : $head, $order);
                    $head ??= $position;
                    $this->assign($period, $position, $name, $division, $order++);
                }
            }
        });
    }

    private function position(string $name, ?Position $parent, int $order): Position
    {
        return Position::updateOrCreate(
            ['context_type' => 'isetial', 'masjid_id' => null, 'name' => $name],
            ['parent_id' => $parent?->id, 'display_order' => $order],
        );
    }

    private function assign(OrganizationPeriod $period, Position $position, string $name, ?Division $division, int $order): void
    {
        $person = Person::firstOrCreate(['name' => $name]);
        PositionAssignment::updateOrCreate(
            ['organization_period_id' => $period->id, 'position_id' => $position->id, 'person_id' => $person->id],
            ['division_id' => $division?->id, 'masjid_id' => null, 'is_active' => true, 'display_order' => $order],
        );
    }
}
