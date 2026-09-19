<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\JerseyOrder;
use App\Models\JerseyPayment;
use App\Models\JerseyProduct;
use App\Models\Masjid;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_site_and_local_masjid_portal_work(): void
    {
        $this->seed();
        $this->get('/')->assertOk()->assertSee('Pemuda-Pemudi 5 Masjid')->assertSee('Pesan Jersey')->assertSee('Cek Status Pesanan');
        $this->get('/m/darulhikmah')->assertOk()->assertSee('Darulhikmah');
        $this->get('/masjid')->assertOk()
            ->assertSee('Darulhikmah')
            ->assertSee("Jami' Darulhakim")
            ->assertSee('Al-fatonah')
            ->assertSee('Jannatussalam')
            ->assertSee('Darulaman');
    }

    public function test_masjid_activity_is_isolated_from_other_portal(): void
    {
        $this->seed();
        $darul = Masjid::whereSlug('darulhikmah')->first();
        $naim = Masjid::whereSlug('naim')->first();
        Activity::create(['masjid_id' => $darul->id, 'title' => 'Rahasia Darul', 'slug' => 'rahasia', 'event_at' => now(), 'published' => true]);
        $this->get('/m/darulhikmah/kegiatan')->assertSee('Rahasia Darul');
        $this->get('/m/naim/kegiatan')->assertDontSee('Rahasia Darul');
    }

    public function test_masjid_admin_cannot_edit_another_masjid(): void
    {
        $this->seed();
        $darul = Masjid::whereSlug('darulhikmah')->first();
        $naim = Masjid::whereSlug('naim')->first();
        $user = User::factory()->create();
        $role = Role::whereSlug('admin-masjid')->first();
        $user->roles()->attach($role->id, ['masjid_id' => $darul->id]);
        $this->actingAs($user)->put(route('admin.masjid.update', $darul), ['name' => 'Darul Baru'])->assertRedirect();
        $this->actingAs($user)->put(route('admin.masjid.update', $naim), ['name' => 'Naim Bobol'])->assertForbidden();
        $this->assertDatabaseMissing('masjids', ['name' => 'Naim Bobol']);
    }

    public function test_only_verified_payments_reduce_balance(): void
    {
        $order = JerseyOrder::create(['order_number' => 'JRS-000001', 'customer_name' => 'A', 'address' => 'X', 'phone' => '081', 'gender' => 'male', 'total' => 200000]);
        JerseyPayment::create(['jersey_order_id' => $order->id, 'amount' => 50000, 'proof_path' => 'x', 'status' => 'pending']);
        JerseyPayment::create(['jersey_order_id' => $order->id, 'amount' => 75000, 'proof_path' => 'y', 'status' => 'verified']);
        $order->load('payments');
        $this->assertSame(75000.0, (float) $order->paid_amount);
        $this->assertSame(125000.0, $order->balance);
        $this->assertSame('Cicilan', $order->payment_status);
    }

    public function test_super_admin_can_open_operational_admin_pages(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->roles()->attach(Role::whereSlug('super-admin')->first()->id);
        $this->actingAs($user)->get(route('admin.members.index'))->assertOk()->assertSeeText('Tambah Anggota');
        $this->actingAs($user)->get(route('admin.content.index'))->assertOk()->assertSee('Tambah Kegiatan');
        $this->actingAs($user)->get(route('admin.structure.index'))->assertOk()->assertSeeText('Tempatkan Person pada Jabatan');
    }

    public function test_masjid_admin_can_create_only_members_for_assigned_masjid(): void
    {
        $this->seed();
        $darul = Masjid::whereSlug('darulhikmah')->first();
        $naim = Masjid::whereSlug('naim')->first();
        $user = User::factory()->create();
        $user->roles()->attach(Role::whereSlug('admin-masjid')->first()->id, ['masjid_id' => $darul->id]);
        $payload = ['name' => 'Anggota Darul', 'member_number' => 'IW-001', 'home_masjid_id' => $darul->id, 'joined_at' => now()->toDateString(), 'is_active' => 1];
        $this->actingAs($user)->post(route('admin.members.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('memberships', ['member_number' => 'IW-001', 'home_masjid_id' => $darul->id]);
        $this->actingAs($user)->post(route('admin.members.store'), [...$payload, 'member_number' => 'IW-002', 'home_masjid_id' => $naim->id])->assertForbidden();
        $this->assertDatabaseMissing('memberships', ['member_number' => 'IW-002']);
    }

    public function test_masjid_admin_cannot_mutate_central_or_other_masjid_content(): void
    {
        $this->seed();
        $darul = Masjid::whereSlug('darulhikmah')->first();
        $naim = Masjid::whereSlug('naim')->first();
        $user = User::factory()->create();
        $user->roles()->attach(Role::whereSlug('admin-masjid')->first()->id, ['masjid_id' => $darul->id]);
        $base = ['title' => 'Agenda', 'slug' => 'agenda', 'description' => 'Uji', 'event_at' => now()->addDay()->format('Y-m-d H:i:s'), 'status' => 'scheduled', 'published' => 1];
        $this->actingAs($user)->post(route('admin.activities.store'), [...$base, 'masjid_id' => $darul->id])->assertRedirect();
        $this->actingAs($user)->post(route('admin.activities.store'), [...$base, 'slug' => 'pusat', 'masjid_id' => null])->assertForbidden();
        $other = Activity::create([...$base, 'slug' => 'naim', 'masjid_id' => $naim->id]);
        $this->actingAs($user)->delete(route('admin.activities.destroy', $other))->assertForbidden();
    }

    public function test_customer_can_order_lookup_and_upload_private_jersey_payment(): void
    {
        Storage::fake('local');
        $this->seed();
        $product = JerseyProduct::with('sizes')->first();
        $plusSize = $product->sizes->firstWhere('name', '5XL');
        $product->sizes->first()->update(['stock' => 4]);
        $plusSize->update(['stock' => 2]);
        $this->get(route('jersey.create'))->assertOk()->assertSee('Simpan Keranjang')->assertSee('Bayar Sekarang')->assertSee('Panduan ukuran')->assertSee('5XL')->assertDontSee('Cek status pesanan sebelumnya');
        $selection = ['jersey_product_id' => $product->id, 'model' => 'Lelaki Pendek', 'sleeve' => 'short', 'quantity' => 1, 'intent' => 'save_cart'];
        $this->postJson(route('jersey.cart.add'), [...$selection, 'jersey_size_id' => $product->sizes->first()->id])->assertOk()->assertJson(['cart_count' => 1]);
        $this->post(route('jersey.cart.add'), [...$selection, 'jersey_size_id' => $plusSize->id, 'model' => 'Muslimah', 'sleeve' => 'long'])->assertRedirect(route('jersey.cart'));
        $cart = session('jersey_cart');
        $lineIds = array_keys($cart);
        $this->patch(route('jersey.cart.update', $lineIds[0]), ['quantity' => 2])->assertRedirect();
        $this->get(route('jersey.cart'))->assertOk()->assertSee('Hapus')->assertSee('Checkout sekarang')->assertDontSee('Perbarui');
        $this->get(route('jersey.checkout'))->assertOk()->assertSee('Data pemesan')->assertSee('Detail jersey');
        $response = $this->post(route('jersey.store'), [
            'customer_name' => 'Pemesan Jersey', 'address' => 'Alamat',
            'phone' => '08123456789', 'cart_line_ids' => $lineIds,
        ]);
        $response->assertSessionHasNoErrors();
        $order = JerseyOrder::with('items')->first();
        $response->assertRedirect(route('jersey.show', $order->order_number));
        $this->assertSame('JRS-000001', $order->order_number);
        $this->assertNull($order->birth_date);
        $this->assertNull($order->gender);
        $this->assertCount(2, $order->items);
        $this->assertSame(3, $order->items->sum('quantity'));
        $this->assertSame(717.0, (float) $order->total);
        $this->assertSame([199.0, 319.0], $order->items->pluck('unit_price')->map(fn ($price) => (float) $price)->all());
        $this->assertSame((float) $order->items->sum('subtotal'), (float) $order->total);
        $this->assertSame(2, $product->sizes->first()->fresh()->stock);
        $this->assertSame(1, $plusSize->fresh()->stock);
        $this->get(route('jersey.show', $order->order_number))->assertOk()->assertSee('Pemesan Jersey');
        $this->post(route('jersey.payment', $order->order_number), ['amount' => 500, 'proof' => UploadedFile::fake()->image('bukti.jpg')])->assertRedirect();
        $payment = JerseyPayment::first();
        $this->assertSame('pending', $payment->status);
        Storage::disk('local')->assertExists($payment->proof_path);
    }

    public function test_order_page_requires_successful_phone_lookup_session(): void
    {
        $order = JerseyOrder::create(['order_number' => 'JRS-000999', 'customer_name' => 'A', 'address' => 'X', 'phone' => '081', 'gender' => 'male', 'total' => 100000]);
        $this->get(route('jersey.show', $order->order_number))->assertForbidden();
        $this->post(route('jersey.find'), ['order_number' => 'jrs-000999', 'phone' => '081'])->assertRedirect(route('jersey.show', $order->order_number));
        $this->get(route('jersey.show', $order->order_number))->assertOk();
    }

    public function test_super_admin_can_configure_jersey_product_and_production_status(): void
    {
        $this->seed();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::whereSlug('super-admin')->first()->id);
        $this->actingAs($admin)->post(route('admin.products.store'), ['name' => 'Jersey Anak', 'description' => 'Khusus anak', 'price' => 120000, 'sizes' => 'S, M'])->assertRedirect();
        $product = JerseyProduct::where('name', 'Jersey Anak')->firstOrFail();
        $this->assertCount(2, $product->sizes);
        $this->actingAs($admin)->patch(route('admin.sizes.update', $product->sizes->first()), ['stock' => 12])->assertRedirect();
        $this->assertSame(12, $product->sizes->first()->fresh()->stock);
        $order = JerseyOrder::create(['order_number' => 'JRS-000010', 'customer_name' => 'A', 'address' => 'X', 'phone' => '081', 'gender' => 'male', 'total' => 120000]);
        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['production_status' => 'ready', 'notes' => 'Siap diambil'])->assertRedirect();
        $this->assertDatabaseHas('jersey_orders', ['id' => $order->id, 'production_status' => 'ready']);
    }
}
