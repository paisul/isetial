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

        foreach ([['Darulhikmah', 'darulhikmah'], ["Jami' Darulhakim", 'jamihakim'], ['Al-fatonah', 'fatimah'], ['Jannatussalam', 'naim'], ['Darulaman', 'aman']] as [$name,$slug]) {
            Masjid::updateOrCreate(['slug' => $slug], ['name' => $name, 'description' => 'Masjid anggota iSetial Wisdom.']);
        }
        $roles = ['Super Admin' => 'super-admin', 'Ketua' => 'ketua', 'Wakil Ketua' => 'wakil-ketua', 'Sekretaris' => 'sekretaris', 'Bendahara' => 'bendahara', 'Pengurus' => 'pengurus', 'Anggota' => 'anggota', 'Admin Masjid' => 'admin-masjid'];
        foreach ($roles as $name => $slug) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
        Guideline::firstOrCreate(['slug' => 'pedoman-organisasi'], ['title' => 'Pedoman Organisasi iSetial Wisdom', 'content' => 'Pedoman organisasi akan diterbitkan dan diperbarui oleh pengurus iSetial Wisdom.', 'published' => true]);
        foreach ([
            ['name' => 'Lelaki Pendek', 'model' => 'Lelaki Pendek', 'sleeve' => 'short', 'price' => 199, 'image_path' => 'images/jersey/model-pria-pendek-v2.jpg'],
            ['name' => 'Lelaki Panjang', 'model' => 'Lelaki Panjang', 'sleeve' => 'long', 'price' => 249, 'image_path' => 'images/jersey/model-pria-panjang-v3.jpg'],
            ['name' => 'Muslimah', 'model' => 'Muslimah', 'sleeve' => 'long', 'price' => 299, 'image_path' => 'images/jersey/model-muslimah-v3.jpg'],
        ] as $data) {
            $product = JerseyProduct::firstOrCreate(['model' => $data['model']], [...$data, 'description' => 'Jersey resmi iSetial Wisdom', 'is_active' => true]);
            foreach (['SS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'] as $size) {
                JerseySize::firstOrCreate(['jersey_product_id' => $product->id, 'name' => $size], ['price_adjustment' => in_array($size, ['3XL', '4XL', '5XL'], true) ? 20 : 0]);
            }
        }
        if (env('SUPER_ADMIN_EMAIL') && env('SUPER_ADMIN_PASSWORD')) {
            $person = Person::firstOrCreate(['phone' => null, 'name' => env('SUPER_ADMIN_NAME', 'Super Admin')]);
            $user = User::updateOrCreate(['email' => env('SUPER_ADMIN_EMAIL')], ['name' => $person->name, 'person_id' => $person->id, 'password' => Hash::make(env('SUPER_ADMIN_PASSWORD'))]);
            $user->roles()->syncWithoutDetaching([Role::where('slug', 'super-admin')->value('id') => ['masjid_id' => null]]);
        }
    }
}
