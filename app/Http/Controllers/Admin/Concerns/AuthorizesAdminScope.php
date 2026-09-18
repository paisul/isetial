<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Masjid;
use App\Models\User;

trait AuthorizesAdminScope
{
    protected function canManageGlobal(User $user): bool
    {
        return $user->isSuperAdmin() || collect(['ketua', 'wakil-ketua', 'sekretaris', 'pengurus'])->contains(fn (string $role) => $user->hasRole($role));
    }

    protected function authorizeGlobal(User $user): void
    {
        abort_unless($this->canManageGlobal($user), 403);
    }

    protected function authorizeMasjid(User $user, Masjid|int $masjid): void
    {
        $id = $masjid instanceof Masjid ? $masjid->id : $masjid;
        abort_unless($user->isSuperAdmin() || $user->hasRole('admin-masjid', $id) || $this->canManageGlobal($user), 403);
    }

    protected function managedMasjidIds(User $user): array
    {
        if ($user->isSuperAdmin() || $this->canManageGlobal($user)) {
            return Masjid::pluck('id')->all();
        }

        return $user->roles()->where('slug', 'admin-masjid')->pluck('role_assignments.masjid_id')->filter()->unique()->values()->all();
    }
}
