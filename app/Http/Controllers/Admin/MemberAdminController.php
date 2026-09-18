<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminScope;
use App\Http\Controllers\Controller;
use App\Models\Masjid;
use App\Models\Membership;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberAdminController extends Controller
{
    use AuthorizesAdminScope;

    public function index(Request $request)
    {
        $ids = $this->managedMasjidIds($request->user());
        abort_if($ids === [], 403);
        $members = Membership::with(['person.user.roles', 'homeMasjid'])->whereIn('home_masjid_id', $ids)->latest()->paginate(20);

        return view('admin.members.index', ['members' => $members, 'masjids' => Masjid::whereIn('id', $ids)->get(), 'roles' => Role::all()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateMember($request);
        $this->authorizeMasjid($request->user(), (int) $data['home_masjid_id']);
        DB::transaction(function () use ($data) {
            $person = Person::create(collect($data)->only(['name', 'gender', 'birth_date', 'phone', 'address'])->all());
            Membership::create([...collect($data)->only(['member_number', 'home_masjid_id', 'joined_at', 'is_active'])->all(), 'person_id' => $person->id]);
        });

        return back()->with('success', 'Anggota ditambahkan.');
    }

    public function update(Request $request, Membership $membership)
    {
        $this->authorizeMasjid($request->user(), $membership->home_masjid_id);
        $data = $this->validateMember($request, $membership);
        $this->authorizeMasjid($request->user(), (int) $data['home_masjid_id']);
        DB::transaction(function () use ($membership, $data) {
            $membership->person->update(collect($data)->only(['name', 'gender', 'birth_date', 'phone', 'address'])->all());
            $membership->update(collect($data)->only(['member_number', 'home_masjid_id', 'joined_at', 'is_active'])->all());
        });

        return back()->with('success', 'Data anggota diperbarui.');
    }

    public function destroy(Request $request, Membership $membership)
    {
        $this->authorizeMasjid($request->user(), $membership->home_masjid_id);
        abort_if($membership->person->user?->id === $request->user()->id, 422, 'Akun sendiri tidak dapat dihapus.');
        $membership->delete();

        return back()->with('success', 'Keanggotaan dinonaktifkan.');
    }

    public function account(Request $request, Membership $membership)
    {
        $this->authorizeMasjid($request->user(), $membership->home_masjid_id);
        $data = $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($membership->person->user?->id)],
            'password' => ['nullable', 'string', 'min:10'],
            'role_ids' => ['array'], 'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);
        if (! $membership->person->user && empty($data['password'])) {
            return back()->withErrors(['password' => 'Password minimal 10 karakter wajib untuk akun baru.']);
        }
        $allowedRoles = $request->user()->isSuperAdmin() ? Role::pluck('id')->all() : Role::whereIn('slug', ['anggota', 'admin-masjid'])->pluck('id')->all();
        abort_if(collect($data['role_ids'] ?? [])->diff($allowedRoles)->isNotEmpty(), 403);
        DB::transaction(function () use ($membership, $data) {
            $values = ['name' => $membership->person->name, 'email' => $data['email']];
            if (! empty($data['password'])) {
                $values['password'] = $data['password'];
            }
            $user = User::updateOrCreate(['person_id' => $membership->person_id], $values);
            $sync = collect($data['role_ids'] ?? [])->mapWithKeys(fn ($id) => [$id => ['masjid_id' => Role::find($id)->slug === 'admin-masjid' ? $membership->home_masjid_id : null]])->all();
            $user->roles()->sync($sync);
        });

        return back()->with('success', 'Akun dan role diperbarui.');
    }

    private function validateMember(Request $request, ?Membership $membership = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'], 'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'], 'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'], 'home_masjid_id' => ['required', 'exists:masjids,id'],
            'member_number' => ['required', 'max:50', Rule::unique('memberships')->ignore($membership?->id)],
            'joined_at' => ['required', 'date'], 'is_active' => ['required', 'boolean'],
        ]);
    }
}
