<?php

use App\Models\Announcement;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;

it('requires authentication to access the search endpoint', function () {
    $this->get(route('search.index'))
        ->assertRedirect(route('login'));
});

it('renders the search page with empty state when no query is provided', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('search.index'))
        ->assertOk()
        ->assertSee('Hãy nhập từ khóa để tìm kiếm')
        ->assertSee('Tìm kiếm trong hệ thống');
});

it('finds matching events, announcements, clubs, and navigation links by keyword', function () {
    $user = User::factory()->create();

    $event = Event::query()->create([
        'title' => 'Ngày hội trí tuệ nhân tạo Phenikaa',
        'description' => 'Sự kiện giao lưu và thuyết trình công nghệ.',
        'location' => 'Hội trường lớn',
        'event_date' => now()->addDays(5),
        'registration_deadline' => now()->addDays(2),
        'max_participants' => 100,
        'status' => 'upcoming',
    ]);

    $announcement = Announcement::query()->create([
        'title' => 'Thông báo học bổng nghiên cứu trí tuệ nhân tạo',
        'body' => 'Chi tiết chương trình học bổng dành cho sinh viên xuất sắc.',
        'category' => 'Học bổng',
        'published_at' => now(),
    ]);

    $club = Club::query()->create([
        'name' => 'CLB Trí tuệ nhân tạo AI Hub',
        'description' => 'Nơi quy tụ sinh viên đam mê AI.',
    ]);

    $response = $this->actingAs($user)
        ->get(route('search.index', ['q' => 'trí tuệ nhân tạo']));

    $response->assertOk()
        ->assertSee('Kết quả cho:')
        ->assertSee('Ngày hội trí tuệ nhân tạo Phenikaa')
        ->assertSee('Thông báo học bổng nghiên cứu trí tuệ nhân tạo')
        ->assertSee('CLB Trí tuệ nhân tạo AI Hub');
});

it('provides json results for live search requests', function () {
    $user = User::factory()->create();

    Event::query()->create([
        'title' => 'Workshop Robot 2026',
        'description' => 'Học chế tạo robot cùng chuyên gia.',
        'location' => 'Phòng thực hành C',
        'event_date' => now()->addDays(3),
        'registration_deadline' => now()->addDay(),
        'max_participants' => 30,
        'status' => 'upcoming',
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('search.index', ['q' => 'Robot', 'format' => 'json']));

    $response->assertOk()
        ->assertJsonPath('query', 'Robot')
        ->assertJsonStructure([
            'query',
            'total',
            'events',
            'announcements',
            'clubs',
            'students',
            'navigation',
        ]);

    expect($response->json('events.0.title'))->toBe('Workshop Robot 2026');
});

it('includes student account records only when accessed by an administrator', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['name' => 'Nguyen Van Dac Biet', 'role' => 'student', 'student_code' => 'SV99999']);
    $normalUser = User::factory()->create(['role' => 'student']);

    $this->actingAs($normalUser)
        ->get(route('search.index', ['q' => 'Nguyen Van Dac Biet']))
        ->assertOk()
        ->assertDontSee('SV99999');

    $this->actingAs($admin)
        ->get(route('search.index', ['q' => 'Nguyen Van Dac Biet']))
        ->assertOk()
        ->assertSee('Nguyen Van Dac Biet')
        ->assertSee('SV99999');
});
