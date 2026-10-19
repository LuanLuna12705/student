<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'title',
    'description',
    'image_url',
    'location',
    'event_date',
    'registration_deadline',
    'max_participants',
    'status',
])]
class Event extends Model
{
    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'registration_deadline' => 'datetime',
        ];
    }

    public function registeredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot('status')
            ->withTimestamps();
    }
}
