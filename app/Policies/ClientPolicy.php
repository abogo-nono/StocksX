<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_client');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can('view_client');
    }

    public function create(User $user): bool
    {
        return $user->can('create_client');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can('update_client');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can('delete_client');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_client');
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->can('force_delete_client');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_client');
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->can('restore_client');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_client');
    }

    public function replicate(User $user, Client $client): bool
    {
        return $user->can('replicate_client');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_client');
    }
}
