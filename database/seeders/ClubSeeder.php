<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // These are starter suggestions, not a verified official roster.
        foreach ([
            'CLB Công nghệ',
            'CLB Học thuật',
            'CLB Nghệ thuật',
            'CLB Thể thao',
            'CLB Tình nguyện',
            'CLB Truyền thông',
            'CLB Tiếng Anh',
        ] as $name) {
            Club::query()->firstOrCreate(['name' => $name]);
        }
    }
}
