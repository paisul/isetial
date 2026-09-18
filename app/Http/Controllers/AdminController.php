<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\JerseyOrder;
use App\Models\JerseyPayment;
use App\Models\Masjid;
use App\Models\Membership;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', ['counts' => ['Anggota' => Membership::count(), 'Masjid' => Masjid::count(), 'Kegiatan' => Activity::count(), 'Pesanan Jersey' => JerseyOrder::count()]]);
    }

    public function masjid(Masjid $masjid)
    {
        $this->authorizeMasjid(request(), $masjid);

        return view('admin.masjid', compact('masjid'));
    }

    public function updateMasjid(Request $r, Masjid $masjid)
    {
        $this->authorizeMasjid($r, $masjid);
        $masjid->update($r->validate(['name' => 'required|max:150', 'description' => 'nullable|max:5000', 'address' => 'nullable|max:1000', 'phone' => 'nullable|max:30', 'email' => 'nullable|email']));

        return back()->with('success', 'Profil masjid diperbarui.');
    }

    public function orders()
    {
        return view('admin.orders', ['orders' => JerseyOrder::with('payments')->latest()->paginate(20)]);
    }

    public function settings()
    {
        return view('admin.settings', ['qr' => Setting::valueOf('payment_qr')]);
    }

    public function updateSettings(Request $r)
    {
        $d = $r->validate(['payment_qr' => 'nullable|image|max:4096']);
        if (isset($d['payment_qr'])) {
            $old = Setting::valueOf('payment_qr');
            if ($old) {
                \Storage::disk('public')->delete($old);
            }$path = $d['payment_qr']->store('settings', 'public');
            Setting::updateOrCreate(['masjid_id' => null, 'key' => 'payment_qr'], ['value' => $path]);
        }

return back()->with('success', 'Pengaturan diperbarui.');
    }

    public function verifyPayment(Request $r, JerseyPayment $payment)
    {
        $d = $r->validate(['status' => 'required|in:verified,rejected', 'notes' => 'nullable|max:1000']);
        $payment->update([...$d, 'verified_by' => $r->user()->id, 'verified_at' => now()]);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }

    private function authorizeMasjid(Request $r, Masjid $masjid): void
    {
        abort_unless($r->user()->isSuperAdmin() || $r->user()->hasRole('admin-masjid', $masjid->id), 403);
    }
}
