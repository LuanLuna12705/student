<?php

namespace App\Services;

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClubApplicationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function apply(User $user, Club $club, array $data): void
    {
        $application = [
            'status' => 'pending',
            'phone' => $data['phone'],
            'facebook_url' => $data['facebook_url'] ?? null,
            'gender' => $data['gender'],
            'cohort' => $data['cohort'] ?? null,
        ];

        $existingMembership = $club->members()->whereKey($user->getKey())->first();

        if ($existingMembership !== null) {
            $club->members()->updateExistingPivot($user->getKey(), $application);

            return;
        }

        $club->members()->attach($user->getKey(), $application);
    }

    public function review(Club $club, User $user, string $status): void
    {
        $isPending = $club->pendingApplications()->whereKey($user->getKey())->exists();

        if (! $isPending) {
            throw new NotFoundHttpException();
        }

        DB::transaction(function () use ($club, $user, $status): void {
            $club->members()->updateExistingPivot($user->getKey(), ['status' => $status]);
        });
    }
}
