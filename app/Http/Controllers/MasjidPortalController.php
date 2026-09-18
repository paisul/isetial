<?php

namespace App\Http\Controllers;

use App\Models\Masjid;
use App\Models\PositionAssignment;

class MasjidPortalController extends Controller
{
    public function home(Masjid $masjid)
    {
        return view('masjid.home', ['masjid' => $masjid, 'activities' => $masjid->activities()->where('published', true)->latest('event_at')->take(4)->get(), 'announcements' => $masjid->announcements()->where('published', true)->latest('published_at')->take(4)->get()]);
    }

    public function profile(Masjid $masjid)
    {
        return view('masjid.profile', compact('masjid'));
    }

    public function dkm(Masjid $masjid)
    {
        $assignments = PositionAssignment::with(['person', 'position', 'period'])->where('masjid_id', $masjid->id)->where('is_active', true)->orderBy('display_order')->get();

        return view('masjid.dkm', compact('masjid', 'assignments'));
    }

    public function activities(Masjid $masjid)
    {
        return view('masjid.activities', ['masjid' => $masjid, 'activities' => $masjid->activities()->where('published', true)->latest('event_at')->paginate(9)]);
    }

    public function announcements(Masjid $masjid)
    {
        return view('masjid.announcements', ['masjid' => $masjid, 'announcements' => $masjid->announcements()->where('published', true)->latest('published_at')->paginate(10)]);
    }

    public function contact(Masjid $masjid)
    {
        return view('masjid.contact', compact('masjid'));
    }
}
