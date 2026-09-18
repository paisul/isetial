<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\JerseyOrder;
use App\Models\Masjid;
use App\Models\Membership;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $masjids = $user->isSuperAdmin() || collect(['ketua', 'wakil-ketua', 'sekretaris', 'pengurus'])->contains(fn ($role) => $user->hasRole($role))
            ? Masjid::all()
            : Masjid::whereIn('id', $user->roles()->where('slug', 'admin-masjid')->pluck('role_assignments.masjid_id'))->get();

        return view('admin.dashboard', [
            'masjids' => $masjids,
            'canGlobal' => $user->isSuperAdmin() || $user->hasRole('ketua') || $user->hasRole('pengurus'),
            'canFinance' => $user->isSuperAdmin() || $user->hasRole('bendahara'),
            'counts' => ['Anggota' => Membership::whereIn('home_masjid_id', $masjids->pluck('id'))->count(), 'Masjid' => $masjids->count(), 'Kegiatan' => Activity::whereIn('masjid_id', $masjids->pluck('id'))->count(), 'Pesanan Jersey' => $user->isSuperAdmin() || $user->hasRole('bendahara') ? JerseyOrder::count() : 0],
        ]);
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
            }
            $path = $d['payment_qr']->store('settings', 'public');
            Setting::updateOrCreate(['masjid_id' => null, 'key' => 'payment_qr'], ['value' => $path]);
        }

        return back()->with('success', 'Pengaturan diperbarui.');
    }

    private function authorizeMasjid(Request $r, Masjid $masjid): void
    {
        abort_unless($r->user()->isSuperAdmin() || $r->user()->hasRole('admin-masjid', $masjid->id), 403);
    }
}
