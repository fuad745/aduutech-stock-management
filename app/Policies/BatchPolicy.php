<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;

class BatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasAnyRole($user, ['Admin', 'Storekeeper', 'Viewer']);
    }

    public function view(User $user, Batch $batch): bool
    {
        return $this->hasAnyRole($user, ['Admin', 'Storekeeper', 'Viewer']);
    }

    public function create(User $user): bool
    {
        return $this->hasAnyRole($user, ['Admin', 'Storekeeper']);
    }

    public function update(User $user, Batch $batch): bool
    {
        return $this->hasAnyRole($user, ['Admin', 'Storekeeper']);
    }

    public function delete(User $user, Batch $batch): bool
    {
        return $this->hasAnyRole($user, ['Admin']);
    }

    public function restore(User $user, Batch $batch): bool
    {
        return $this->hasAnyRole($user, ['Admin']);
    }

    public function forceDelete(User $user, Batch $batch): bool
    {
        return $this->hasAnyRole($user, ['Admin']);
    }

    private function hasAnyRole(User $user, array $roles): bool
    {
        if (!method_exists($user, 'hasRole')) {
            return true;
        }
        return $user->hasRole($roles);
    }
}
