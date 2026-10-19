<?php

namespace App\Services;

use App\Models\Announcement;

class AnnouncementService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Announcement
    {
        return Announcement::query()->create([
            ...$data,
            'published_at' => now(),
        ]);
    }

    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }
}
