<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JerseyController;
use App\Http\Controllers\MasjidPortalController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/tentang', [PublicController::class, 'about'])->name('about');
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
    Route::get('/admin/jersey', [AdminController::class, 'orders'])->middleware('role:super-admin,bendahara')->name('admin.orders');
    Route::patch('/admin/pembayaran/{payment}', [AdminController::class, 'verifyPayment'])->middleware('role:super-admin,bendahara')->name('admin.payment.verify');
    Route::get('/admin/pembayaran/{payment}/bukti', [JerseyController::class, 'proof'])->name('admin.payment.proof');
    Route::get('/admin/pengaturan', [AdminController::class, 'settings'])->middleware('role:super-admin')->name('admin.settings');
    Route::post('/admin/pengaturan', [AdminController::class, 'updateSettings'])->middleware('role:super-admin')->name('admin.settings.update');
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
