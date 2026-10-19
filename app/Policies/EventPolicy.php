<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function register(User $user, Event $event): bool
    {
        if (! in_array($user->role, [UserRole::Student, UserRole::ClubLeader], true)) {
            return false;
        }

        if ($event->registration_deadline->isPast() || $event->event_date->isPast()) {
            return false;
        }

        if ($event->registeredUsers()->whereKey($user->id)->exists()) {
            return false;
        }

        return $event->registeredUsers()->count() < $event->max_participants;
    }

    public function unregister(User $user, Event $event): bool
    {
        return $event->registeredUsers()->whereKey($user->id)->exists();
    }
}
