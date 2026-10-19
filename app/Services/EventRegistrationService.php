<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;

class EventRegistrationService
{
    /**
     * @return array{success: bool, message: string}
     */
    public function register(User $user, Event $event): array
    {
        if ($event->registration_deadline->isPast() || $event->event_date->isPast()) {
            return [
                'success' => false,
                'message' => 'Sự kiện đã hết hạn đăng ký hoặc đã diễn ra.',
            ];
        }

        if ($event->registeredUsers()->whereKey($user->id)->exists()) {
            return [
                'success' => false,
                'message' => 'Bạn đã đăng ký sự kiện này rồi.',
            ];
        }

        if ($event->registeredUsers()->count() >= $event->max_participants) {
            return [
                'success' => false,
                'message' => 'Sự kiện đã đủ số lượng người tham gia.',
            ];
        }

        $event->registeredUsers()->attach($user->id, ['status' => 'registered']);

        return [
            'success' => true,
            'message' => 'Bạn đã đăng ký thành công sự kiện.',
        ];
    }

    public function unregister(User $user, Event $event): void
    {
        $user->registeredEvents()->detach($event->id);
    }
}
