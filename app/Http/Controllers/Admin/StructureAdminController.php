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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StructureAdminController extends Controller
{
    use AuthorizesAdminScope;

    public function index(Request $request)
    {
        $ids = $this->managedMasjidIds($request->user());
        $canGlobal = $this->canManageGlobal($request->user());
        $scope = fn ($q) => $q->whereIn('masjid_id', $ids)->when($canGlobal, fn ($q) => $q->orWhereNull('masjid_id'));

        return view('admin.structure.index', [
            'periods' => OrganizationPeriod::with('masjid')->where($scope)->orderByDesc('starts_at')->get(),
            'positions' => Position::with('parent')->where($scope)->orderBy('display_order')->get(),
            'assignments' => PositionAssignment::with(['person', 'position.parent', 'division', 'period', 'masjid'])->where($scope)->orderBy('display_order')->get(),
            'masjids' => Masjid::whereIn('id', $ids)->get(),
            'people' => Person::where(function ($query) use ($ids, $canGlobal) {
                $query->whereHas('membership', fn ($q) => $q->whereIn('home_masjid_id', $ids));
                if ($canGlobal) {
                    $query->orWhereHas('positions', fn ($q) => $q->whereNull('masjid_id'));
                }
            })->orderBy('name')->get(),
            'divisions' => Division::where($scope)->orderBy('name')->get(),
            'canGlobal' => $canGlobal,
        ]);
    }

    public function storePeriod(Request $request)
    {
        $data = $this->validatePeriod($request);
        $this->authorizeScope($request, $data['masjid_id'] ?? null);
        $this->savePeriod(new OrganizationPeriod, $data);

        return back()->with('success', 'Periode ditambahkan.');
    }

    public function updatePeriod(Request $request, OrganizationPeriod $period)
    {
        $this->authorizeScope($request, $period->masjid_id);
        $data = $this->validatePeriod($request);
        abort_if(($data['masjid_id'] ?? null) != $period->masjid_id, 422, 'Lingkup periode tidak dapat dipindahkan.');
        $this->savePeriod($period, $data);

        return back()->with('success', 'Periode diperbarui.');
    }

    public function destroyPeriod(Request $request, OrganizationPeriod $period)
    {
        $this->authorizeScope($request, $period->masjid_id);
        abort_if($period->assignments()->exists(), 422, 'Periode yang masih memiliki pengurus tidak dapat dihapus.');
        $period->delete();

        return back()->with('success', 'Periode dihapus.');
    }

    public function storePosition(Request $request)
    {
        $data = $this->validatePosition($request);
        $this->normalizePositionScope($data);
        $this->authorizeScope($request, $data['masjid_id']);
        $this->ensureParentMatches($data);
        Position::create($data);

        return back()->with('success', 'Jabatan ditambahkan.');
    }

    public function updatePosition(Request $request, Position $position)
    {
        $this->authorizeScope($request, $position->masjid_id);
        $data = $this->validatePosition($request, $position);
        $this->normalizePositionScope($data);
        abort_if($data['masjid_id'] != $position->masjid_id, 422, 'Lingkup jabatan tidak dapat dipindahkan.');
        $this->ensureParentMatches($data, $position);
        $position->update($data);

        return back()->with('success', 'Jabatan diperbarui.');
    }

    public function destroyPosition(Request $request, Position $position)
    {
        $this->authorizeScope($request, $position->masjid_id);
        abort_if($position->assignments()->exists(), 422, 'Jabatan yang masih digunakan tidak dapat dihapus.');
        $position->delete();

        return back()->with('success', 'Jabatan dihapus.');
    }

    public function storeDivision(Request $request)
    {
        $data = $this->validateDivision($request);
        $this->authorizeScope($request, $data['masjid_id'] ?? null);
        Division::create($data);

        return back()->with('success', 'Bidang ditambahkan.');
    }

    public function updateDivision(Request $request, Division $division)
    {
        $this->authorizeScope($request, $division->masjid_id);
        $data = $this->validateDivision($request);
        abort_if(($data['masjid_id'] ?? null) != $division->masjid_id, 422, 'Lingkup bidang tidak dapat dipindahkan.');
        $division->update($data);

        return back()->with('success', 'Bidang diperbarui.');
    }

    public function destroyDivision(Request $request, Division $division)
    {
        $this->authorizeScope($request, $division->masjid_id);
        abort_if($division->assignments()->exists(), 422, 'Bidang yang masih digunakan tidak dapat dihapus.');
        $division->delete();

        return back()->with('success', 'Bidang dihapus.');
    }

    public function storeAssignment(Request $request)
    {
        $data = $this->validateAssignment($request);
        unset($data['photo']);
        $period = OrganizationPeriod::findOrFail($data['organization_period_id']);
        $this->authorizeScope($request, $period->masjid_id);
        $this->ensureAssignmentMatches($data, $period);
        $this->savePhoto($request, (int) $data['person_id']);
        PositionAssignment::create([...$data, 'masjid_id' => $period->masjid_id]);

        return back()->with('success', 'Pengurus ditempatkan.');
    }

    public function updateAssignment(Request $request, PositionAssignment $assignment)
    {
        $this->authorizeScope($request, $assignment->masjid_id);
        $data = $this->validateAssignment($request);
        unset($data['photo']);
        $period = OrganizationPeriod::findOrFail($data['organization_period_id']);
        abort_if($period->masjid_id !== $assignment->masjid_id, 422, 'Lingkup penempatan tidak dapat dipindahkan.');
        $this->ensureAssignmentMatches($data, $period);
        $this->savePhoto($request, (int) $data['person_id']);
        $assignment->update($data);

        return back()->with('success', 'Penempatan diperbarui.');
    }

    public function destroyAssignment(Request $request, PositionAssignment $assignment)
    {
        $this->authorizeScope($request, $assignment->masjid_id);
        $assignment->delete();

        return back()->with('success', 'Penempatan dihapus.');
    }

    private function validatePeriod(Request $request): array
    {
        return $request->validate([
            'masjid_id' => ['nullable', 'exists:masjids,id'], 'name' => ['required', 'max:150'],
            'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
        ]);
    }

    private function savePeriod(OrganizationPeriod $period, array $data): void
    {
        DB::transaction(function () use ($period, $data) {
            if ($data['status'] === 'active') {
                $query = OrganizationPeriod::where('masjid_id', $data['masjid_id'] ?? null);
                if ($period->exists) {
                    $query->whereKeyNot($period->getKey());
                }
                $query->update(['status' => 'archived', 'is_active' => false]);
            }
            $period->fill([...$data, 'is_active' => $data['status'] === 'active'])->save();
        });
    }

    private function validatePosition(Request $request, ?Position $position = null): array
    {
        return $request->validate([
            'context_type' => ['required', Rule::in(['isetial', 'dkm'])], 'masjid_id' => ['nullable', 'exists:masjids,id'],
            'parent_id' => ['nullable', 'exists:positions,id', Rule::notIn(array_filter([$position?->id]))],
            'name' => ['required', 'max:150'], 'display_order' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function normalizePositionScope(array &$data): void
    {
        if ($data['context_type'] === 'dkm') {
            abort_if(empty($data['masjid_id']), 422, 'Jabatan DKM wajib memiliki masjid.');
        } else {
            $data['masjid_id'] = null;
        }
    }

    private function ensureParentMatches(array $data, ?Position $position = null): void
    {
        if (! empty($data['parent_id'])) {
            $parent = Position::findOrFail($data['parent_id']);
            abort_if($parent->masjid_id != $data['masjid_id'] || $parent->context_type !== $data['context_type'], 422, 'Induk jabatan harus berada pada lingkup yang sama.');
            abort_if($position && $parent->parent_id === $position->id, 422, 'Hierarki jabatan tidak boleh membentuk lingkaran.');
        }
    }

    private function validateDivision(Request $request): array
    {
        return $request->validate(['masjid_id' => ['nullable', 'exists:masjids,id'], 'name' => ['required', 'max:150'], 'description' => ['nullable', 'max:2000']]);
    }

    private function validateAssignment(Request $request): array
    {
        return $request->validate([
            'person_id' => ['required', 'exists:people,id'], 'organization_period_id' => ['required', 'exists:organization_periods,id'],
            'position_id' => ['required', 'exists:positions,id'], 'division_id' => ['nullable', 'exists:divisions,id'],
            'is_active' => ['required', 'boolean'], 'display_order' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:3072'],
        ]);
    }

    private function ensureAssignmentMatches(array $data, OrganizationPeriod $period): void
    {
        $position = Position::findOrFail($data['position_id']);
        abort_if($period->masjid_id !== $position->masjid_id, 422, 'Konteks periode dan jabatan tidak cocok.');
        if (! empty($data['division_id'])) {
            abort_if(Division::findOrFail($data['division_id'])->masjid_id !== $period->masjid_id, 422, 'Bidang tidak berada pada lingkup yang sama.');
        }
        if ($period->masjid_id) {
            abort_unless(Person::findOrFail($data['person_id'])->membership?->home_masjid_id === $period->masjid_id, 422, 'Person bukan anggota masjid ini.');
        }
    }

    private function savePhoto(Request $request, int $personId): void
    {
        if ($path = $request->file('photo')?->store('people', 'public')) {
            Person::findOrFail($personId)->update(['photo' => $path]);
        }
    }

    private function authorizeScope(Request $request, ?int $masjidId): void
    {
        $masjidId ? $this->authorizeMasjid($request->user(), $masjidId) : $this->authorizeGlobal($request->user());
    }
}
