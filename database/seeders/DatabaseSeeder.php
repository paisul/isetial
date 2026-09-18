<?php

namespace Database\Seeders;

use App\Models\Guideline;
use App\Models\JerseyProduct;
use App\Models\JerseySize;
use App\Models\Masjid;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        foreach ([['Darul Hikmah', 'darulhikmah'], ['Jami Hakim', 'jamihakim'], ['Fatimah', 'fatimah'], ['Naim', 'naim'], ['Aman', 'aman']] as [$name,$slug]) {
            Masjid::firstOrCreate(['slug' => $slug], ['name' => $name, 'description' => 'Masjid anggota iSetial Wisdom.']);
        }
        $roles = ['Super Admin' => 'super-admin', 'Ketua' => 'ketua', 'Wakil Ketua' => 'wakil-ketua', 'Sekretaris' => 'sekretaris', 'Bendahara' => 'bendahara', 'Pengurus' => 'pengurus', 'Anggota' => 'anggota', 'Admin Masjid' => 'admin-masjid'];
        foreach ($roles as $name => $slug) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
        Guideline::firstOrCreate(['slug' => 'pedoman-organisasi'], ['title' => 'Pedoman Organisasi iSetial Wisdom', 'content' => 'Pedoman organisasi akan diterbitkan dan diperbarui oleh pengurus iSetial Wisdom.', 'published' => true]);
        $product = JerseyProduct::firstOrCreate(['name' => 'Jersey iSetial Wisdom'], ['description' => 'Jersey resmi iSetial Wisdom', 'price' => 150000, 'is_active' => true]);
        foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size) {
            JerseySize::firstOrCreate(['jersey_product_id' => $product->id, 'name' => $size]);
        }
        if (env('SUPER_ADMIN_EMAIL') && env('SUPER_ADMIN_PASSWORD')) {
            $person = Person::firstOrCreate(['phone' => null, 'name' => env('SUPER_ADMIN_NAME', 'Super Admin')]);
            $user = User::updateOrCreate(['email' => env('SUPER_ADMIN_EMAIL')], ['name' => $person->name, 'person_id' => $person->id, 'password' => Hash::make(env('SUPER_ADMIN_PASSWORD'))]);
            $user->roles()->syncWithoutDetaching([Role::where('slug', 'super-admin')->value('id') => ['masjid_id' => null]]);
        }
    }
}
