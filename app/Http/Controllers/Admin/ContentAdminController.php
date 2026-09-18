<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminScope;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Guideline;
use App\Models\Masjid;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentAdminController extends Controller
{
    use AuthorizesAdminScope;

    public function index(Request $request)
    {
        $ids = $this->managedMasjidIds($request->user());
        $activities = Activity::with('masjid')->where(fn ($q) => $q->whereIn('masjid_id', $ids)->when($this->canManageGlobal($request->user()), fn ($q) => $q->orWhereNull('masjid_id')))->latest('event_at')->paginate(15, ['*'], 'activities');
        $announcements = Announcement::with('masjid')->whereIn('masjid_id', $ids)->latest()->paginate(15, ['*'], 'announcements');
        $guidelines = $this->canManageGlobal($request->user()) ? Guideline::latest()->get() : collect();

        return view('admin.content.index', compact('activities', 'announcements', 'guidelines') + ['masjids' => Masjid::whereIn('id', $ids)->get(), 'canGlobal' => $this->canManageGlobal($request->user())]);
    }

    public function storeActivity(Request $request)
    {
        $data = $this->activityData($request);
        $this->authorizeContentScope($request, $data['masjid_id'] ?? null);
        Activity::create($data);

        return back()->with('success', 'Kegiatan ditambahkan.');
    }

    public function updateActivity(Request $request, Activity $activity)
    {
        $this->authorizeContentScope($request, $activity->masjid_id);
        $data = $this->activityData($request, $activity);
        $this->authorizeContentScope($request, $data['masjid_id'] ?? null);
        $activity->update($data);

        return back()->with('success', 'Kegiatan diperbarui.');
    }

    public function destroyActivity(Request $request, Activity $activity)
    {
        $this->authorizeContentScope($request, $activity->masjid_id);
        $activity->delete();

        return back()->with('success', 'Kegiatan dihapus.');
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate(['masjid_id' => ['required', 'exists:masjids,id'], 'title' => ['required', 'max:200'], 'content' => ['required', 'max:10000'], 'published' => ['required', 'boolean']]);
        $this->authorizeMasjid($request->user(), (int) $data['masjid_id']);
        Announcement::create([...$data, 'published_at' => $data['published'] ? now() : null]);

        return back()->with('success', 'Pengumuman ditambahkan.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $this->authorizeMasjid($request->user(), $announcement->masjid_id);
        $data = $request->validate(['title' => ['required', 'max:200'], 'content' => ['required', 'max:10000'], 'published' => ['required', 'boolean']]);
        $announcement->update([...$data, 'published_at' => $data['published'] ? ($announcement->published_at ?? now()) : null]);

        return back()->with('success', 'Pengumuman diperbarui.');
    }

    public function destroyAnnouncement(Request $request, Announcement $announcement)
    {
        $this->authorizeMasjid($request->user(), $announcement->masjid_id);
        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }

    public function storeGuideline(Request $request)
    {
        $this->authorizeGlobal($request->user());
        Guideline::create($request->validate(['title' => ['required', 'max:200'], 'slug' => ['required', 'alpha_dash', 'unique:guidelines,slug'], 'content' => ['required'], 'published' => ['required', 'boolean']]));

        return back()->with('success', 'Pedoman ditambahkan.');
    }

    public function updateGuideline(Request $request, Guideline $guideline)
    {
        $this->authorizeGlobal($request->user());
        $guideline->update($request->validate(['title' => ['required', 'max:200'], 'slug' => ['required', 'alpha_dash', Rule::unique('guidelines')->ignore($guideline->id)], 'content' => ['required'], 'published' => ['required', 'boolean']]));

        return back()->with('success', 'Pedoman diperbarui.');
    }

    public function destroyGuideline(Request $request, Guideline $guideline)
    {
        $this->authorizeGlobal($request->user());
        $guideline->delete();

        return back()->with('success', 'Pedoman dihapus.');
    }

    private function authorizeContentScope(Request $request, ?int $masjidId): void
    {
        $masjidId ? $this->authorizeMasjid($request->user(), $masjidId) : $this->authorizeGlobal($request->user());
    }

    private function activityData(Request $request, ?Activity $activity = null): array
    {
        return $request->validate([
            'masjid_id' => ['nullable', 'exists:masjids,id'], 'title' => ['required', 'max:200'],
            'slug' => ['required', 'alpha_dash', Rule::unique('activities')->where(fn ($q) => $q->where('masjid_id', $request->input('masjid_id')))->ignore($activity?->id)],
            'description' => ['nullable', 'max:20000'], 'event_at' => ['required', 'date'], 'location' => ['nullable', 'max:200'],
            'status' => ['required', 'in:draft,scheduled,completed,cancelled'], 'published' => ['required', 'boolean'],
        ]);
    }
}
