<?php

use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\JerseyAdminController;
use App\Http\Controllers\Admin\MemberAdminController;
use App\Http\Controllers\Admin\StructureAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JerseyController;
use App\Http\Controllers\MasjidPortalController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/tentang', [PublicController::class, 'about'])->name('about');
Route::get('/struktur-pengurus', [PublicController::class, 'structure'])->name('structure');
Route::get('/masjid', [PublicController::class, 'masjids'])->name('masjids');
Route::get('/pedoman', [PublicController::class, 'guidelines'])->name('guidelines');
Route::get('/kegiatan', [PublicController::class, 'activities'])->name('activities');
Route::get('/kegiatan/{activity:slug}', [PublicController::class, 'activity'])->name('activity');
Route::get('/kontak', [PublicController::class, 'contact'])->name('contact');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::prefix('jersey')->name('jersey.')->group(function () {
    Route::get('/pesan', [JerseyController::class, 'create'])->name('create');
    Route::post('/pesan', [JerseyController::class, 'store'])->middleware('throttle:10,1')->name('store');
    Route::post('/keranjang', [JerseyController::class, 'addToCart'])->name('cart.add');
    Route::get('/keranjang', [JerseyController::class, 'cart'])->name('cart');
    Route::patch('/keranjang/{line}', [JerseyController::class, 'updateCart'])->name('cart.update');
    Route::delete('/keranjang/{line}', [JerseyController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [JerseyController::class, 'checkout'])->name('checkout');
    Route::get('/cek', [JerseyController::class, 'lookup'])->name('lookup');
    Route::post('/cek', [JerseyController::class, 'find'])->middleware('throttle:8,1')->name('find');
    Route::get('/pesanan/{order}', [JerseyController::class, 'show'])->name('show');
    Route::post('/pesanan/{order}/bayar', [JerseyController::class, 'payment'])->middleware('throttle:5,1')->name('payment');
});
Route::middleware('auth')->group(function () {
    Route::get('/saya', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/saya/profil', [MemberController::class, 'profile'])->name('member.profile');
    Route::get('/admin', [AdminController::class, 'index'])->middleware('role:super-admin,ketua,wakil-ketua,sekretaris,bendahara,pengurus,admin-masjid')->name('admin.dashboard');
    Route::get('/admin/masjid/{masjid}', [AdminController::class, 'masjid'])->name('admin.masjid');
    Route::put('/admin/masjid/{masjid}', [AdminController::class, 'updateMasjid'])->name('admin.masjid.update');
    Route::get('/admin/jersey', [JerseyAdminController::class, 'index'])->middleware('role:super-admin,bendahara')->name('admin.orders');
    Route::post('/admin/jersey/produk', [JerseyAdminController::class, 'storeProduct'])->middleware('role:super-admin')->name('admin.products.store');
    Route::put('/admin/jersey/produk/{product}', [JerseyAdminController::class, 'updateProduct'])->middleware('role:super-admin')->name('admin.products.update');
    Route::post('/admin/jersey/produk/{product}/ukuran', [JerseyAdminController::class, 'storeSize'])->middleware('role:super-admin')->name('admin.sizes.store');
    Route::patch('/admin/jersey/ukuran/{size}', [JerseyAdminController::class, 'updateSize'])->middleware('role:super-admin')->name('admin.sizes.update');
    Route::patch('/admin/jersey/pesanan/{order}', [JerseyAdminController::class, 'updateOrder'])->middleware('role:super-admin,bendahara')->name('admin.orders.update');
    Route::patch('/admin/pembayaran/{payment}', [JerseyAdminController::class, 'verifyPayment'])->middleware('role:super-admin,bendahara')->name('admin.payment.verify');
    Route::get('/admin/pembayaran/{payment}/bukti', [JerseyController::class, 'proof'])->name('admin.payment.proof');
    Route::get('/admin/pengaturan', [AdminController::class, 'settings'])->middleware('role:super-admin')->name('admin.settings');
    Route::post('/admin/pengaturan', [AdminController::class, 'updateSettings'])->middleware('role:super-admin')->name('admin.settings.update');
    Route::prefix('admin')->name('admin.')->middleware('role:super-admin,ketua,wakil-ketua,sekretaris,pengurus,admin-masjid')->group(function () {
        Route::get('/anggota', [MemberAdminController::class, 'index'])->name('members.index');
        Route::post('/anggota', [MemberAdminController::class, 'store'])->name('members.store');
        Route::put('/anggota/{membership}', [MemberAdminController::class, 'update'])->name('members.update');
        Route::delete('/anggota/{membership}', [MemberAdminController::class, 'destroy'])->name('members.destroy');
        Route::put('/anggota/{membership}/akun', [MemberAdminController::class, 'account'])->name('members.account');
        Route::get('/konten', [ContentAdminController::class, 'index'])->name('content.index');
        Route::post('/kegiatan', [ContentAdminController::class, 'storeActivity'])->name('activities.store');
        Route::put('/kegiatan/{activity}', [ContentAdminController::class, 'updateActivity'])->name('activities.update');
        Route::delete('/kegiatan/{activity}', [ContentAdminController::class, 'destroyActivity'])->name('activities.destroy');
        Route::post('/pengumuman', [ContentAdminController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::put('/pengumuman/{announcement}', [ContentAdminController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/pengumuman/{announcement}', [ContentAdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');
        Route::post('/pedoman', [ContentAdminController::class, 'storeGuideline'])->name('guidelines.store');
        Route::put('/pedoman/{guideline}', [ContentAdminController::class, 'updateGuideline'])->name('guidelines.update');
        Route::delete('/pedoman/{guideline}', [ContentAdminController::class, 'destroyGuideline'])->name('guidelines.destroy');
        Route::get('/struktur', [StructureAdminController::class, 'index'])->name('structure.index');
        Route::post('/struktur/periode', [StructureAdminController::class, 'storePeriod'])->name('periods.store');
        Route::put('/struktur/periode/{period}', [StructureAdminController::class, 'updatePeriod'])->name('periods.update');
        Route::delete('/struktur/periode/{period}', [StructureAdminController::class, 'destroyPeriod'])->name('periods.destroy');
        Route::post('/struktur/jabatan', [StructureAdminController::class, 'storePosition'])->name('positions.store');
        Route::put('/struktur/jabatan/{position}', [StructureAdminController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/struktur/jabatan/{position}', [StructureAdminController::class, 'destroyPosition'])->name('positions.destroy');
        Route::post('/struktur/divisi', [StructureAdminController::class, 'storeDivision'])->name('divisions.store');
        Route::put('/struktur/divisi/{division}', [StructureAdminController::class, 'updateDivision'])->name('divisions.update');
        Route::delete('/struktur/divisi/{division}', [StructureAdminController::class, 'destroyDivision'])->name('divisions.destroy');
        Route::post('/struktur/penempatan', [StructureAdminController::class, 'storeAssignment'])->name('assignments.store');
        Route::put('/struktur/penempatan/{assignment}', [StructureAdminController::class, 'updateAssignment'])->name('assignments.update');
        Route::delete('/struktur/penempatan/{assignment}', [StructureAdminController::class, 'destroyAssignment'])->name('assignments.destroy');
    });
});

$portal = function () {
    Route::get('/', [MasjidPortalController::class, 'home'])->name('home');
    Route::get('/profil', [MasjidPortalController::class, 'profile'])->name('profile');
    Route::get('/dkm', [MasjidPortalController::class, 'dkm'])->name('dkm');
    Route::get('/kegiatan', [MasjidPortalController::class, 'activities'])->name('activities');
    Route::get('/pengumuman', [MasjidPortalController::class, 'announcements'])->name('announcements');
    Route::get('/kontak', [MasjidPortalController::class, 'contact'])->name('contact');
};
Route::domain('{masjid}.'.config('app.domain', 'isetial.test'))->middleware('masjid')->name('portal.')->group($portal);
Route::prefix('m/{masjid:slug}')->middleware('masjid')->name('local.portal.')->group($portal);
