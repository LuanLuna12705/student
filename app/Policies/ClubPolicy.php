<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Club $club): bool
    {
        return $user->isAdmin() || $user->is($club->leader) || $user->clubs()->whereKey($club->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Club $club): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Club $club): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Club $club): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Club $club): bool
    {
        return $user->isAdmin();
    }

    public function manageClubLeaderAccess(User $user): bool
    {
        return $user->isClubLeader();
    }

    public function manageMembership(User $user, Club $club): bool
    {
        return $user->isClubLeader() && $club->leader_id === $user->id;
    }

    public function reviewApplication(User $user, Club $club): bool
    {
        return $this->manageMembership($user, $club);
    }
}
