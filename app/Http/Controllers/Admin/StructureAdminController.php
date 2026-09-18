<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminScope;
use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Masjid;
use App\Models\OrganizationPeriod;
use App\Models\Person;
use App\Models\Position;
use App\Models\PositionAssignment;
use Illuminate\Http\Request;

class StructureAdminController extends Controller
{
    use AuthorizesAdminScope;

    public function index(Request $request)
    {
        $ids = $this->managedMasjidIds($request->user());
        $canGlobal = $this->canManageGlobal($request->user());
        $periods = OrganizationPeriod::with('masjid')->where(fn ($q) => $q->whereIn('masjid_id', $ids)->when($canGlobal, fn ($q) => $q->orWhereNull('masjid_id')))->get();
        $positions = Position::where(fn ($q) => $q->whereIn('masjid_id', $ids)->when($canGlobal, fn ($q) => $q->orWhereNull('masjid_id')))->get();
        $assignments = PositionAssignment::with(['person', 'position', 'division', 'period'])->where(fn ($q) => $q->whereIn('masjid_id', $ids)->when($canGlobal, fn ($q) => $q->orWhereNull('masjid_id')))->orderBy('display_order')->get();

        return view('admin.structure.index', compact('periods', 'positions', 'assignments', 'canGlobal') + [
            'masjids' => Masjid::whereIn('id', $ids)->get(),
            'people' => Person::whereHas('membership', fn ($q) => $q->whereIn('home_masjid_id', $ids))->orderBy('name')->get(),
            'divisions' => Division::where(fn ($q) => $q->whereIn('masjid_id', $ids)->when($canGlobal, fn ($q) => $q->orWhereNull('masjid_id')))->get(),
        ]);
    }

    public function storePeriod(Request $request)
    {
        $data = $request->validate(['masjid_id' => ['nullable', 'exists:masjids,id'], 'name' => ['required', 'max:150'], 'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at'], 'is_active' => ['required', 'boolean']]);
        $this->authorizeScope($request, $data['masjid_id'] ?? null);
        OrganizationPeriod::create($data);

        return back()->with('success', 'Periode ditambahkan.');
    }

    public function storePosition(Request $request)
    {
        $data = $request->validate(['context_type' => ['required', 'in:isetial,dkm'], 'masjid_id' => ['nullable', 'exists:masjids,id'], 'name' => ['required', 'max:150'], 'display_order' => ['required', 'integer', 'min:0']]);
        if ($data['context_type'] === 'dkm') {
            abort_if(empty($data['masjid_id']), 422, 'Jabatan DKM wajib memiliki masjid.');
        }
        if ($data['context_type'] === 'isetial') {
            $data['masjid_id'] = null;
        }
        $this->authorizeScope($request, $data['masjid_id']);
        Position::create($data);

        return back()->with('success', 'Jabatan ditambahkan.');
    }

    public function storeDivision(Request $request)
    {
        $data = $request->validate(['masjid_id' => ['nullable', 'exists:masjids,id'], 'name' => ['required', 'max:150'], 'description' => ['nullable', 'max:2000']]);
        $this->authorizeScope($request, $data['masjid_id'] ?? null);
        Division::create($data);

        return back()->with('success', 'Divisi ditambahkan.');
    }

    public function storeAssignment(Request $request)
    {
        $data = $request->validate(['person_id' => ['required', 'exists:people,id'], 'organization_period_id' => ['required', 'exists:organization_periods,id'], 'position_id' => ['required', 'exists:positions,id'], 'division_id' => ['nullable', 'exists:divisions,id'], 'is_active' => ['required', 'boolean'], 'display_order' => ['required', 'integer', 'min:0']]);
        $period = OrganizationPeriod::findOrFail($data['organization_period_id']);
        $position = Position::findOrFail($data['position_id']);
        abort_if($period->masjid_id !== $position->masjid_id, 422, 'Konteks periode dan jabatan tidak cocok.');
        $this->authorizeScope($request, $period->masjid_id);
        if ($period->masjid_id) {
            abort_unless(Person::findOrFail($data['person_id'])->membership?->home_masjid_id === $period->masjid_id, 422, 'Person bukan anggota masjid ini.');
        }
        PositionAssignment::create([...$data, 'masjid_id' => $period->masjid_id]);

        return back()->with('success', 'Penempatan jabatan ditambahkan.');
    }

    public function destroyAssignment(Request $request, PositionAssignment $assignment)
    {
        $this->authorizeScope($request, $assignment->masjid_id);
        $assignment->delete();

        return back()->with('success', 'Penempatan dihapus.');
    }

    private function authorizeScope(Request $request, ?int $masjidId): void
    {
        $masjidId ? $this->authorizeMasjid($request->user(), $masjidId) : $this->authorizeGlobal($request->user());
    }
}
