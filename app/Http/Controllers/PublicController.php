<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Guideline;
use App\Models\Masjid;
use App\Models\OrganizationPeriod;
use App\Models\PositionAssignment;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home', ['masjids' => Masjid::where('is_active', true)->get(), 'activities' => Activity::whereNull('masjid_id')->where('published', true)->latest('event_at')->take(3)->get()]);
    }

    public function about()
    {
        return view('public.about', ['leaders' => PositionAssignment::with(['person', 'position', 'division'])->whereNull('masjid_id')->where('is_active', true)->whereHas('period', fn ($query) => $query->where('status', 'active'))->orderBy('display_order')->get()]);
    }

    public function structure()
    {
        $period = OrganizationPeriod::whereNull('masjid_id')->where('status', 'active')->latest('starts_at')->first();
        $assignments = $period
            ? PositionAssignment::with(['person', 'position', 'division'])->whereNull('masjid_id')->where('organization_period_id', $period->id)->where('is_active', true)->orderBy('display_order')->get()
            : collect();
        $positions = $assignments->pluck('position')->unique('id')->sortBy('display_order');

        return view('public.structure', compact('period', 'assignments', 'positions'));
    }

    public function masjids()
    {
        return view('public.masjids', ['masjids' => Masjid::where('is_active', true)->get()]);
    }

    public function guidelines()
    {
        return view('public.guidelines', ['guidelines' => Guideline::where('published', true)->get()]);
    }

    public function activities()
    {
        return view('public.activities', ['activities' => Activity::whereNull('masjid_id')->where('published', true)->latest('event_at')->paginate(9)]);
    }

    public function activity(Activity $activity)
    {
        abort_if($activity->masjid_id || ! $activity->published, 404);

        return view('public.activity', compact('activity'));
    }

    public function contact()
    {
        return view('public.contact');
    }
}
