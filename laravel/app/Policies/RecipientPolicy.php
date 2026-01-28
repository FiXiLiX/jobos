<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Recipient;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipientPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Recipient');
    }

    public function view(AuthUser $authUser, Recipient $recipient): bool
    {
        return $authUser->can('View:Recipient');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Recipient');
    }

    public function update(AuthUser $authUser, Recipient $recipient): bool
    {
        return $authUser->can('Update:Recipient');
    }

    public function delete(AuthUser $authUser, Recipient $recipient): bool
    {
        return $authUser->can('Delete:Recipient');
    }

    public function restore(AuthUser $authUser, Recipient $recipient): bool
    {
        return $authUser->can('Restore:Recipient');
    }

    public function forceDelete(AuthUser $authUser, Recipient $recipient): bool
    {
        return $authUser->can('ForceDelete:Recipient');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Recipient');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Recipient');
    }

    public function replicate(AuthUser $authUser, Recipient $recipient): bool
    {
        return $authUser->can('Replicate:Recipient');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Recipient');
    }

}