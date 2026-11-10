<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','iSetial Wisdom')</title>
    <meta name="description" content="Pemuda-Pemudi 5 Masjid">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=20260919">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=20260919">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script defer src="{{ asset('js/translations.js') }}?v=20261110.1"></script>
    <script defer src="{{ asset('js/locale.js') }}?v=20261110.4"></script>
    <style>[x-cloak]{display:none}.prose p{margin:.75rem 0}</style>
</head>
<body class="bg-slate-50 text-slate-800">
<nav class="sticky top-0 z-20 bg-emerald-950 text-white shadow">
    <div class="relative mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl font-black tracking-tight">
            <img src="{{ asset('favicon.png') }}?v=20260919" alt="Logo iSetial Wisdom" width="42" height="42" class="h-[42px] w-[42px] rounded-full object-cover" loading="eager">
            <span>iSETIAL <span class="text-amber-400">WISDOM</span></span>
        </a>
        <button id="nav-toggle" type="button" onclick="toggleMobileNav()" class="rounded-lg border border-emerald-700 px-3 py-2 text-xl leading-none md:hidden" aria-label="Buka navigasi" aria-controls="nav" aria-expanded="false">☰</button>
        <div id="nav" class="mobile-nav absolute left-0 top-full hidden w-full flex-col gap-1 bg-emerald-950 px-4 pb-5 pt-2 text-sm shadow-xl md:static md:flex md:w-auto md:flex-row md:items-center md:gap-5 md:bg-transparent md:p-0 md:shadow-none">
            @if(isset($activeMasjid))
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.home':'portal.home',$activeMasjid) }}">Beranda</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.profile':'portal.profile',$activeMasjid) }}">Profil</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.dkm':'portal.dkm',$activeMasjid) }}">DKM</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.activities':'portal.activities',$activeMasjid) }}">Kegiatan</a>
                <a href="{{ route(request()->routeIs('local.*')?'local.portal.announcements':'portal.announcements',$activeMasjid) }}">Pengumuman</a>
            @else
                <a href="{{ route('about') }}">Tentang</a>
                <a href="{{ route('structure') }}">Pengurus</a>
                <a href="{{ route('masjids') }}">Masjid</a>
                <a href="{{ route('guidelines') }}">Pedoman</a>
                <a href="{{ route('activities') }}">Kegiatan</a>
                <a href="{{ route('jersey.create') }}">Jersey</a>
                <a href="{{ route('contact') }}">Kontak</a>
            @endif
            @auth
                <a href="{{ route('member.dashboard') }}">Portal Saya</a>
                <form method="post" action="{{route('logout')}}">@csrf<button>Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="font-bold text-amber-400">Login</a>
            @endauth
            <div data-language-switcher data-no-translate class="mt-2 flex w-fit items-center gap-1 rounded-full border border-emerald-700 bg-emerald-900/70 p-1 md:mt-0" role="group" aria-label="Pilih bahasa / เลือกภาษา">
                <button type="button" data-locale="id" class="rounded-full px-2.5 py-1 text-xs font-black transition">ID</button>
                <button type="button" data-locale="th" class="rounded-full px-2.5 py-1 text-xs font-black transition">ไทย</button>
            </div>
        </div>
    </div>
</nav>
<script>
    function toggleMobileNav() {
        const menu = document.getElementById('nav');
        const button = document.getElementById('nav-toggle');
        const isOpen = menu.classList.toggle('is-open');
        menu.classList.toggle('hidden', !isOpen);
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        button.textContent = isOpen ? '✕' : '☰';
    }
</script>
@if(session('success'))
    <div class="mx-auto mt-4 max-w-5xl rounded-xl bg-emerald-100 px-4 py-3 text-emerald-900">{{session('success')}}</div>
@endif
@if($errors->any())
    <div class="mx-auto mt-4 max-w-5xl rounded-xl bg-red-100 px-4 py-3 text-red-900"><ul>@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
@endif
<main>@yield('content')</main>
<footer class="mt-16 bg-emerald-950 px-4 py-10 text-center text-sm text-emerald-100">�<�h��춻�q�^tug('darulhikmah')->first();
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
        $muslimahProduct = JerseyProduct::with('sizes')->where('model', 'Muslimah')->firstOrFail();
        $agent = JerseyAgent::create(['name' => 'Petugas Kampung', 'phone' => '0811', 'token' => str_repeat('a', 48)]);
        $this->get(route('jersey.agent.entry', $agent->token))->assertRedirect(route('jersey.create'));
        $plusSize = $muslimahProduct->sizes->firstWhere('name', '5XL');
        $product->sizes->first()->update(['stock' => 4]);
        $plusSize->update(['stock' => 2]);
        $this->get(route('jersey.create'))->assertOk()->assertSee('Simpan Keranjang')->assertSee('Bayar Sekarang')->assertSee('id="checkout-sheet"', false)->assertSee('Data pemesan')->assertSee('Panduan ukuran')->assertSee('5XL')->assertDontSee('Cek status pesanan sebelumnya');
        $selection = ['jersey_product_id' => $product->id, 'model' => 'Lelaki Pendek', 'sleeve' => 'short', 'quantity' => 1, 'intent' => 'save_cart'];
        $saved = $this->postJson(route('jersey.cart.add'), [...$selection, 'jersey_size_id' => $product->sizes->first()->id])->assertOk()->assertJson(['cart_count' => 1])->assertJsonStructure(['line_id', 'next_url']);
        $saved->assertJson(['next_url' => route('jersey.checkout', ['lines' => [$saved->json('line_id')]])]);
        $this->post(route('jersey.cart.add'), [...$selection, 'jersey_product_id' => $muslimahProduct->id, 'jersey_size_id' => $plusSize->id, 'model' => 'Muslimah', 'sleeve' => 'long'])->assertRedirect(route('jersey.cart'));
        $buyResponse = $this->postJson(route('jersey.cart.add'), [...$selection, 'jersey_size_id' => $product->sizes->first()->id, 'intent' => 'buy_now'])->assertOk();
        $cart = session('jersey_cart');
        $this->assertCount(2, $cart);
        $this->assertSame(3, collect($cart)->sum('quantity'));
        $buyLine = $buyResponse->json('line_id');
        $this->assertSame($saved->json('line_id'), $buyLine);
        $buyResponse->assertJson(['cart_count' => 3, 'next_url' => route('jersey.cart')]);
        $this->get(route('jersey.cart'))->assertOk()->assertSee('Lelaki Pendek')->assertSee('Muslimah')->assertSee('Bayar penuh sekarang');
        $lineIds = array_keys($cart);
        $this->patch(route('jersey.cart.update', $lineIds[0]), ['quantity' => 2])->assertRedirect();
        $this->get(route('jersey.cart'))->assertOk()->assertSee('Hapus')->assertSee('Bayar penuh sekarang')->assertSee(route('jersey.checkout'))->assertSee('fixed inset-x-0 bottom-0', false)->assertDontSee('Perbarui');
        $this->get(route('jersey.checkout'))->assertOk()->assertSee('Data pemesan')->assertSee('Lanjut ke Pembayaran')->assertSee('Lanjut Bayar')->assertSee('Bayar melalui PromptPay')->assertSee('Unggah Bukti & Selesaikan Pesanan', false)->assertSee('data-mobile-action="payment"', false)->assertSee('fixed inset-x-0 bottom-0', false)->assertDontSee('Kirim Pesanan & Bukti', false)->assertSee('data-checkout-step="payment" class="hidden', false)->assertSee('Detail jersey')->assertSee('Lelaki Pendek')->assertSee('Muslimah')->assertSee('092-671-1380');
        session(['jersey_payment_code' => 42]);
        $this->post(route('jersey.store'), ['customer_name' => 'Pemesan Jersey', 'address' => 'Alamat', 'phone' => '08123456789', 'cart_line_ids' => $lineIds])
            ->assertSessionHasErrors(['proof' => 'Unggah bukti pembayaran setelah memindai QR PromptPay.']);
        $response = $this->post(route('jersey.store'), [
            'customer_name' => 'Pemesan Jersey', 'address' => 'Alamat',
            'phone' => '08123456789', 'cart_line_ids' => $lineIds,
            'proof' => UploadedFile::fake()->image('bayar-penuh.jpg'),
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
        $this->assertSame($agent->id, $order->jersey_agent_id);
        $this->assertSame(42, $order->payment_code);
        $this->assertSame(717.42, $order->payable_total);
        $this->assertSame($order->payable_total, (float) $order->payments->first()->amount);
        $this->assertSame(2, $product->sizes->first()->fresh()->stock);
        $this->assertSame(1, $plusSize->fresh()->stock);
        $this->get(route('jersey.show', $order->order_number))->assertOk()->assertSee('Pemesan Jersey')->assertSee('Menunggu verifikasi')->assertSee('Pesanan diterima')->assertSee('Perjalanan pesanan')->assertSee('Rincian jersey')->assertSee('Ringkasan pembayaran')->assertSee('Petugas Kampung')->assertDontSee('Progres pembayaran')->assertDontSee('Masih sisa');
        $payment = JerseyPayment::first();
        $this->assertSame('pending', $payment->status);
        Storage::disk('local')->assertExists($payment->proof_path);
    }

    public function test_admin_manages_up_to_ten_jersey_agents(): void
    {
        $this->seed();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::whereSlug('super-admin')->first()->id);
        foreach (range(1, 10) as $number) {
            $this->actingAs($admin)->post(route('admin.agents.store'), ['name' => "Petugas {$number}", 'phone' => "080{$number}"])->assertRedirect();
        }
        $this->assertSame(10, JerseyAgent::count());
        $this->assertNotNull(JerseyAgent::first()->management_token);
        $this->actingAs($admin)->post(route('admin.agents.store'), ['name' => 'Petugas 11'])->assertStatus(422);
        $this->actingAs($admin)->get(route('admin.orders'))->assertOk()->assertSee('Petugas (10/10)')->assertSee('Petugas 1')->assertSee('Link dashboard pribadi petugas');
    }

    public function test_public_jersey_link_creates_a_general_order_without_an_agent(): void
    {
        Storage::fake('local');
        $this->seed();
        $product = JerseyProduct::with('sizes')->firstOrFail();
        $selection = ['jersey_product_id' => $product->id, 'jersey_size_id' => $product->sizes->first()->id, 'model' => 'Lelaki Pendek', 'sleeve' => 'short', 'quantity' => 1, 'intent' => 'save_cart'];

        $this->get(route('jersey.create'))->assertOk();
        $this->postJson(route('jersey.cart.add'), $selection)->assertOk();
        $lineIds = array_keys(session('jersey_cart'));
        session(['jersey_payment_code' => 7]);
        $response = $this->post(route('jersey.store'), [
            'customer_name' => 'Pemesan Umum', 'address' => 'Alamat Umum', 'phone' => '0800000000',
            'cart_line_ids' => $lineIds, 'proof' => UploadedFile::fake()->image('umum.jpg'),
        ]);

        $order = JerseyOrder::where('customer_name', 'Pemesan Umum')->firstOrFail();
        $response->assertRedirect(route('jersey.show', $order->order_number));
        $this->assertNull($order->jersey_agent_id);
        $this->get(route('jersey.show', $order->order_number))->assertOk()->assertSee('Umum');
    }

    public function test_agent_dashboard_only_exposes_orders_and_proofs_from_its_private_link(): void
    {
        Storage::fake('local');
        $agent = JerseyAgent::create(['name' => 'Petugas Satu', 'token' => str_repeat('a', 48), 'management_token' => str_repeat('m', 48)]);
        $other = JerseyAgent::create(['name' => 'Petugas Dua', 'token' => str_repeat('b', 48), 'management_token' => str_repeat('n', 48)]);
        $order = JerseyOrder::create(['jersey_agent_id' => $agent->id, 'order_number' => 'JRS-AGENT-1', 'customer_name' => 'Warga Satu', 'address' => 'Kampung A', 'phone' => '0811', 'total' => 199]);
        $otherOrder = JerseyOrder::create(['jersey_agent_id' => $other->id, 'order_number' => 'JRS-AGENT-2', 'customer_name' => 'Warga Rahasia', 'address' => 'Kampung B', 'phone' => '0822', 'total' => 299]);
        Storage::disk('local')->put('jersey-proofs/one.jpg', 'proof-one');
        Storage::disk('local')->put('jersey-proofs/two.jpg', 'proof-two');
        $payment = JerseyPayment::create(['jersey_order_id' => $order->id, 'amount' => 199, 'proof_path' => 'jersey-proofs/one.jpg', 'status' => 'verified']);
        $otherPayment = JerseyPayment::create(['jersey_order_id' => $otherOrder->id, 'amount' => 299, 'proof_path' => 'jersey-proofs/two.jpg', 'status' => 'verified']);

        $this->get(route('jersey.agent.dashboard', $agent->management_token))->assertOk()->assertSee('Petugas Satu')->assertSee('Warga Satu')->assertDontSee('Warga Rahasia')->assertSee('฿199.00');
        $this->get(route('jersey.agent.proof', [$agent->management_token, $payment]))->assertOk();
        $this->get(route('jersey.agent.proof', [$agent->management_token, $otherPayment]))->assertNotFound();
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
        Storage::fake('public');
        $this->seed();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::whereSlug('super-admin')->first()->id);
        $this->actingAs($admin)->post(route('admin.settings.update'), ['promptpay_number' => '099-123-4567'])->assertRedirect();
        $this->assertSame('099-123-4567', Setting::valueOf('promptpay_number'));
        $this->actingAs($admin)->get(route('admin.settings'))->assertOk()->assertSee('099-123-4567')->assertSee('Pengaturan Pembayaran');
        $qrUrl = URL::temporarySignedRoute('jersey.promptpay.qr', now()->addMinute(), ['amount' => '717.42']);
        $this->get($qrUrl)->assertOk()->assertHeader('Content-Type', 'image/svg+xml')->assertSee('<svg', false);
        $this->actingAs($admin)->post(route('admin.products.store'), ['name' => 'Jersey Anak', 'model' => 'Lelaki Pendek', 'description' => 'Khusus anak', 'price' => 120, 'sizes' => 'S, M'])->assertRedirect();
        $product = JerseyProduct::where('name', 'Jersey Anak')->firstOrFail();
        $this->assertCount(2, $product->sizes);
        $this->get(route('jersey.create'))->assertOk()->assertSee('Jersey Anak')->assertSee('"price":120', false);
        $this->actingAs($admin)->patch(route('admin.sizes.update', $product->sizes->first()), ['stock' => 12, 'price_adjustment' => 5])->assertRedirect();
        $this->assertSame(12, $product->sizes->first()->fresh()->stock);
        $this->assertSame(5.0, (float) $product->sizes->first()->fresh()->price_adjustment);
        Storage::disk('public')->put('jersey/products/old.jpg', 'old photo');
        $product->update(['image_path' => 'jersey/products/old.jpg']);
        $this->actingAs($admin)->put(route('admin.products.update', $product), ['name' => 'Jersey Anak', 'model' => 'Lelaki Pendek', 'description' => 'Khusus anak', 'price' => 120, 'is_active' => 0, 'image' => UploadedFile::fake()->image('baru.jpg')])->assertRedirect();
        Storage::disk('public')->assertMissing('jersey/products/old.jpg');
        Storage::disk('public')->assertExists($product->fresh()->image_path);
        $this->actingAs($admin)->get(route('admin.orders'))->assertOk()->assertSee('Unduh foto');
        $this->get(route('jersey.create'))->assertOk()->assertDontSee('Jersey Anak');
        $order = JerseyOrder::create(['order_number' => 'JRS-000010', 'customer_name' => 'A', 'address' => 'X', 'phone' => '081', 'gender' => 'male', 'total' => 120000]);
        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['production_status' => 'ready', 'notes' => 'Siap diambil'])->assertRedirect();
        $this->assertDatabaseHas('jersey_orders', ['id' => $order->id, 'production_status' => 'ready']);
        $this->actingAs($admin)->delete(route('admin.orders.destroy', $order))->assertRedirect();
        $this->assertSoftDeleted('jersey_orders', ['id' => $order->id]);
    }

    public function test_jersey_admin_can_search_and_filter_orders(): void
    {
        $this->seed();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::whereSlug('super-admin')->first()->id);
        JerseyOrder::create(['order_number' => 'JRS-SEARCH-1', 'customer_name' => 'Ahmad Firdaus', 'address' => 'X', 'phone' => '08123', 'total' => 100000, 'production_status' => 'ready']);
        $queuedOrder = JerseyOrder::create(['order_number' => 'JRS-SEARCH-2', 'customer_name' => 'Budi', 'address' => 'Y', 'phone' => '08999', 'total' => 100000, 'production_status' => 'queued']);
        $product = JerseyProduct::with('sizes')->firstOrFail();
        $queuedOrder->items()->create(['jersey_product_id' => $product->id, 'jersey_size_id' => $product->sizes->first()->id, 'model' => 'Lelaki Pendek', 'sleeve' => 'short', 'quantity' => 3, 'unit_price' => 199, 'subtotal' => 597]);

        $this->actingAs($admin)->get(route('admin.orders', ['q' => 'Ahmad', 'production' => 'ready']))
            ->assertOk()
            ->assertSeeInOrder(['Pesanan', 'Pemesan', 'Item', 'Pembayaran', 'Produksi', 'Tanggal', 'Aksi'])
            ->assertSee('JRS-SEARCH-1')
            ->assertDontSee('JRS-SEARCH-2')
            ->assertDontSee('Sisa ฿')
            ->assertDontSee('<option value="unpaid"', false)
            ->assertDontSee('<option value="partial"', false)
            ->assertSee('Total dana diterima')
            ->assertSee('Lelaki panjang')
            ->assertSee('Lelaki pendek')
            ->assertSee('Muslimah')
            ->assertSee('Data Baju')
            ->assertSee('Data Baju untuk Produksi')
            ->assertSee('Total dibuat')
            ->assertSee('data-admin-panel="production-data"', false)
            ->assertSee('sticky top-[74px]', false)
            ->assertSee('space-y-3 md:hidden', false)
            ->assertSee('Menampilkan 1 pesanan');
    }
}
