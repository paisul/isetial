<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\JerseyOrder;
use App\Models\JerseyPayment;
use App\Models\Masjid;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_site_and_local_masjid_portal_work(): void
    {
        $this->seed();
        $this->get('/')->assertOk()->assertSee('Pemuda-Pemudi 5 Masjid');
        $this->get('/m/darulhikmah')->assertOk()->assertSee('Darul Hikmah');
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
}
