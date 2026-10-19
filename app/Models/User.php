<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'avatar', 'student_code', 'major', 'class_name', 'date_of_birth', 'gender'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'date_of_birth' => 'date',
        ];
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isClubLeader(): bool
    {
        return $this->role === UserRole::ClubLeader;
    }

    public function getAvatarUrlAttribute(): string
    {
        if (! empty($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?? 'SV').'&background=233871&color=ffffff';
    }

    public function registeredEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)
            ->withPivot(['status', 'phone', 'facebook_url', 'gender', 'cohort'])
            ->withTimestamps();
    }

    public function managedClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'leader_id');
    }
}
