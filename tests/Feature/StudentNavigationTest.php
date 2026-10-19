<?php

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DemoEventStudentSeeder;
use Illuminate\Support\Facades\Hash;

it('casts user roles to the app enum', function () {
    $user = User::factory()->create(['role' => UserRole::ClubLeader->value]);

    expect($user->role)->toBeInstanceOf(UserRole::class)
        ->and($user->role)->toBe(UserRole::ClubLeader)
        ->and($user->isClubLeader())->toBeTrue();
});

it('renders every student navigation destination from the dashboard', function () {
    $user = User::factory()->create();

    $dashboard = $this->actingAs($user)->get(route('dashboard'));

    $destinations = [
        'announcements.index' => 'Tin tức',
        'events.joined' => 'Sự kiện đang đăng ký',
        'activities.scores' => 'Tra cứu hoạt động',
        'events.index' => 'Đăng ký sự kiện',
        'events.calendar' => 'Lịch sự kiện',
        'clubs.index' => 'Câu lạc bộ của tôi',
        'clubs.register' => 'Đăng ký câu lạc bộ',
    ];

    foreach ($destinations as $routeName => $title) {
        $dashboard->assertSee(route($routeName), false);
        $this->get(route($routeName))->assertSee($title);
    }

    $dashboard->assertDontSee('href="#"', false);
    $dashboard->assertDontSee('href="/tuition"', false);
});

it('shows up to three upcoming events with registration still open on the dashboard', function () {
    $user = User::factory()->create();

    foreach ([1, 2, 3, 4] as $daysFromNow) {
        Event::query()->create([
            'title' => 'Sự kiện sắp tới '.$daysFromNow,
            'description' => 'Sự kiện kiểm thử.',
            'location' => 'Hội trường A',
            'event_date' => now()->addDays($daysFromNow),
            'registration_deadline' => now()->addHours(12),
            'max_participants' => 20,
            'status' => 'upcoming',
        ]);
    }

    Event::query()->create([
        'title' => 'Sự kiện đã đóng đăng ký',
        'description' => 'Sự kiện kiểm thử.',
        'location' => 'Hội trường B',
        'event_date' => now()->addDay(),
        'registration_deadline' => now()->subMinute(),
        'max_participants' => 20,
        'status' => 'upcoming',
    ]);

    Event::query()->create([
        'title' => 'Sự kiện đã diễn ra',
        'description' => 'Sự kiện kiểm thử.',
        'location' => 'Hội trường C',
        'event_date' => now()->subDay(),
        'registration_deadline' => now()->addHours(12),
        'max_participants' => 20,
        'status' => 'upcoming',
    ]);

    $dashboard = $this->actingAs($user)->get(route('dashboard'));

    $dashboard
        ->assertSee('Sự kiện sắp tới 1')
        ->assertSee('Sự kiện sắp tới 2')
        ->assertSee('Sự kiện sắp tới 3')
        ->assertDontSee('Sự kiện sắp tới 4')
        ->assertDontSee('Sự kiện đã đóng đăng ký')
        ->assertDontSee('Sự kiện đã diễn ra');
});

it('shows student details and the latest extracurricular score section', function () {
    $user = User::factory()->create(['name' => 'Nguyen Van A']);

    $this->actingAs($user)
        ->get(route('activities.scores'))
        ->assertSee('Thông tin sinh viên')
        ->assertSee('Nguyen Van A')
        ->assertSee('Mã số:')
        ->assertSee('Điểm ngoại khóa')
        ->assertSee('Sự kiện đã đăng ký')
        ->assertSee('Đăng ký sự kiện không đồng nghĩa với đã tham dự')
        ->assertSee('Bạn chưa đăng ký sự kiện nào.');
});

it('groups registered events by the selected academic calendar', function () {
    $this->travelTo(new DateTimeImmutable('2026-10-17T12:00:00+07:00'));
    $student = User::factory()->create();

    $events = collect([
        ['title' => 'Sự kiện học kỳ I', 'date' => '2026-11-10 09:00:00', 'term' => 'Học kỳ I 2026-2027'],
        ['title' => 'Sự kiện học kỳ II', 'date' => '2027-03-10 09:00:00', 'term' => 'Học kỳ II 2026-2027'],
        ['title' => 'Sự kiện học kỳ hè', 'date' => '2027-07-10 09:00:00', 'term' => 'Học kỳ hè 2026-2027'],
    ])->map(function (array $data) use ($student): array {
        $event = Event::query()->create([
            'title' => $data['title'],
            'description' => 'Sự kiện kiểm thử.',
            'location' => 'Hội trường A',
            'event_date' => $data['date'],
            'registration_deadline' => '2026-10-01 09:00:00',
            'max_participants' => 20,
            'status' => 'upcoming',
        ]);

        $student->registeredEvents()->attach($event, ['status' => 'registered']);

        return $data;
    });

    $response = $this->actingAs($student)->get(route('activities.scores'));

    foreach ($events as $event) {
        $response
            ->assertSee($event['title'])
            ->assertSee($event['term']);
    }

    $response->assertSee('hệ thống không tự quy đổi điểm');
});

it('shows student management only to admins and protects its route', function () {
    $student = User::factory()->create();
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertDontSee(route('admin.students.index'), false)
        ->assertDontSee(route('admin.administrators.index'), false);

    $this->get(route('admin.students.index'))->assertForbidden();
    $this->get(route('admin.administrators.index'))->assertForbidden();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertSee(route('admin.students.index'), false)
        ->assertSee(route('admin.administrators.index'), false);

    $this->get(route('admin.students.index'))
        ->assertOk()
        ->assertSee($student->name)
        ->assertDontSee($admin->email);
});

it('creates the admin user from configured credentials', function () {
    config([
        'admin.name' => 'Campus Admin',
        'admin.email' => 'admin@example.test',
        'admin.password' => 'test-password-123',
    ]);

    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();

    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->email_verified_at)->not->toBeNull();
});

it('lets admins manage student accounts and view their registrations', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student']);
    $clubLeader = User::factory()->create(['role' => 'club_leader']);

    $event = Event::query()->create([
        'title' => 'Ngày hội công nghệ',
        'description' => 'Sự kiện demo cho test.',
        'location' => 'Hội trường A',
        'event_date' => '2026-10-15 09:00:00',
        'registration_deadline' => '2026-10-12 23:59:00',
        'max_participants' => 50,
        'status' => 'upcoming',
    ]);

    $student->registeredEvents()->attach($event->id, ['status' => 'registered']);

    $this->actingAs($admin)
        ->get(route('admin.students.index'))
        ->assertDontSee('Số lượng sự kiện đã đăng ký')
        ->assertDontSee('Trạng thái')
        ->assertDontSee('Khóa')
        ->assertSee('w-[16%]', false)
        ->assertSee($student->name)
        ->assertSee($clubLeader->name)
        ->assertSee('Tổng số tài khoản')
        ->assertSee('data-stat-value="1"', false)
        ->assertSee('data-stat-value="3"', false)
        ->assertSee('Danh sách sinh viên và trưởng CLB')
        ->assertSee('2 kết quả (không gồm quản trị viên)')
        ->assertSee('Thêm sinh viên')
        ->assertSee('Lưu thay đổi')
        ->assertSee('bg-orange-500', false)
        ->assertSee('form="bulk-role-form"', false)
        ->assertSee('name="roles['.$student->id.']"', false)
        ->assertDontSee('value="admin"', false);

    $this->actingAs($admin)
        ->post(route('admin.students.store'), [
            'name' => 'Sinh viên mới',
            'email' => 'newstudent@example.com',
            'password' => 'password123',
        ])
        ->assertRedirect(route('admin.students.index'));

    $this->actingAs($admin)
        ->patch(route('admin.students.role', $student), ['role' => 'admin'])
        ->assertSessionHasErrors('role');

    expect($student->fresh()->role)->toBe(UserRole::Student);

    $this->actingAs($admin)
        ->get(route('admin.students.show', $student))
        ->assertSee('Danh sách sự kiện đã đăng ký')
        ->assertSee('Ngày hội công nghệ')
        ->assertDontSee('Trạng thái tài khoản')
        ->assertDontSee('Hoạt động');

    $this->actingAs($admin)
        ->patch('/admin/students/'.$student->id.'/status', ['status' => 'locked'])
        ->assertNotFound();
});

it('saves role changes for all submitted students together', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $firstStudent = User::factory()->create(['role' => 'student']);
    $secondStudent = User::factory()->create(['role' => 'student']);

    $this->actingAs($admin)
        ->patch('/admin/students/roles', [
            'roles' => [
                $firstStudent->id => 'club_leader',
                $secondStudent->id => 'student',
            ],
        ])
        ->assertRedirect(route('admin.students.index'));

    expect($firstStudent->fresh()->role->value)->toBe(UserRole::ClubLeader->value)
        ->and($secondStudent->fresh()->role->value)->toBe(UserRole::Student->value);
});

it('lets admins create administrator accounts and view the administrator list', function () {
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Existing Admin']);
    $student = User::factory()->create(['role' => 'student', 'name' => 'Student Not Listed']);

    $this->actingAs($admin)
        ->get(route('admin.administrators.index'))
        ->assertSee('Existing Admin')
        ->assertDontSee('Student Not Listed')
        ->assertSee('Tạo tài khoản quản trị')
        ->assertSee('Danh sách quản trị viên');

    $this->actingAs($admin)
        ->post(route('admin.administrators.store'), [
            'name' => 'New Admin',
            'email' => 'new-admin@example.test',
            'password' => 'secret-password',
        ])
        ->assertRedirect(route('admin.administrators.index'));

    $createdAdministrator = User::query()->where('email', 'new-admin@example.test')->firstOrFail();

    expect($createdAdministrator->role)->toBe(UserRole::Admin)
        ->and(Hash::check('secret-password', $createdAdministrator->password))->toBeTrue();
});

it('rejects administrator role changes from student-management endpoints', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student']);
    $anotherAdmin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->patch(route('admin.students.role', $student), ['role' => 'admin'])
        ->assertSessionHasErrors('role');

    $this->patch(route('admin.students.roles'), [
        'roles' => [$student->id => 'admin'],
    ])->assertSessionHasErrors('roles.'.$student->id);

    $this->patch(route('admin.students.role', $anotherAdmin), ['role' => 'student'])
        ->assertNotFound();

    expect($student->fresh()->role)->toBe(UserRole::Student)
        ->and($anotherAdmin->fresh()->role)->toBe(UserRole::Admin);
});

it('filters students by name and role in the admin list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $studentAlpha = User::factory()->create(['name' => 'Nguyen Van A', 'email' => 'alpha@example.com', 'role' => 'student']);
    $studentBravo = User::factory()->create(['name' => 'Tran Thi B', 'email' => 'bravo@example.com', 'role' => 'student']);
    $leader = User::factory()->create(['name' => 'Le Van Leader', 'email' => 'leader@example.com', 'role' => 'club_leader']);

    $this->actingAs($admin)
        ->get(route('admin.students.index', ['search' => 'Nguyen']))
        ->assertSee($studentAlpha->name)
        ->assertDontSee($studentBravo->name)
        ->assertDontSee($leader->name);

    $this->actingAs($admin)
        ->get(route('admin.students.index', ['role' => 'club_leader']))
        ->assertSee($leader->name)
        ->assertDontSee($studentAlpha->name)
        ->assertDontSee($studentBravo->name);
});

it('allows students to search and filter events by status', function () {
    $student = User::factory()->create(['role' => 'student']);

    $openEvent = Event::query()->create([
        'title' => 'Sự kiện mở đăng ký',
        'description' => 'Mô tả sự kiện mở.',
        'location' => 'Hội trường A',
        'event_date' => now()->addDays(5),
        'registration_deadline' => now()->addDays(3),
        'max_participants' => 30,
        'status' => 'upcoming',
    ]);

    $closedEvent = Event::query()->create([
        'title' => 'Sự kiện đã đóng',
        'description' => 'Mô tả sự kiện đã đóng.',
        'location' => 'Phòng B',
        'event_date' => now()->subDay(),
        'registration_deadline' => now()->subDays(2),
        'max_participants' => 20,
        'status' => 'upcoming',
    ]);

    $this->actingAs($student)
        ->get(route('events.index', ['search' => 'mở']))
        ->assertSee('Sự kiện mở đăng ký')
        ->assertDontSee('Sự kiện đã đóng');

    $this->actingAs($student)
        ->get(route('events.index', ['status' => 'closed']))
        ->assertSee('Sự kiện đã đóng')
        ->assertDontSee('Sự kiện mở đăng ký');

    $this->actingAs($student)
        ->get(route('events.index', ['status' => 'open']))
        ->assertSee($openEvent->title)
        ->assertDontSee($closedEvent->title);
});

it('allows only admins to create and delete announcements', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student']);

    $this->actingAs($admin)
        ->post('/announcements', [
            'title' => 'Thông báo hội thảo',
            'body' => 'Nội dung hội thảo dành cho sinh viên.',
            'category' => 'club-events',
        ])
        ->assertRedirect('/announcements');

    $this->assertDatabaseHas('announcements', ['title' => 'Thông báo hội thảo']);
    $announcementId = Announcement::query()->where('title', 'Thông báo hội thảo')->value('id');

    $this->actingAs($student)
        ->get('/announcements')
        ->assertSee('Thông báo hội thảo')
        ->assertDontSee('Thêm tin tức');

    $this->post('/announcements', [
        'title' => 'Tin không được phép',
        'body' => 'Nội dung',
        'category' => 'general',
    ])
        ->assertForbidden();

    $this->delete('/announcements/'.$announcementId)->assertForbidden();

    $this->actingAs($admin)
        ->get('/announcements')
        ->assertSee('Thêm tin tức')
        ->assertSee('Xóa tin tức');

    $this->delete('/announcements/'.$announcementId)->assertRedirect('/announcements');
    $this->assertDatabaseMissing('announcements', ['id' => $announcementId]);
});

it('allows only admins to create and delete events', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student']);
    $eventDate = now()->addDays(3)->format('Y-m-d\\TH:i');
    $registrationDeadline = now()->addDays(2)->format('Y-m-d\\TH:i');

    $this->actingAs($admin)
        ->post('/events', [
            'title' => 'Ngày hội việc làm',
            'description' => 'Kết nối sinh viên với doanh nghiệp.',
            'location' => 'Hội trường lớn',
            'event_date' => $eventDate,
            'registration_deadline' => $registrationDeadline,
            'max_participants' => 120,
        ])
        ->assertRedirect('/events');

    $this->assertDatabaseHas('events', ['title' => 'Ngày hội việc làm']);
    $eventId = Event::query()->where('title', 'Ngày hội việc làm')->value('id');

    $this->actingAs($student)
        ->get('/events')
        ->assertSee('Ngày hội việc làm')
        ->assertDontSee('Thêm sự kiện');

    $this->post('/events', [
        'title' => 'Sự kiện trái phép',
        'description' => 'Nội dung',
        'location' => 'Hội trường',
        'event_date' => $eventDate,
        'registration_deadline' => $registrationDeadline,
        'max_participants' => 30,
    ])
        ->assertForbidden();

    $this->delete('/events/'.$eventId)->assertForbidden();

    $this->actingAs($admin)
        ->get('/events')
        ->assertSee('Thêm sự kiện')
        ->assertSee('Xóa sự kiện');

    $this->delete('/events/'.$eventId)->assertRedirect('/events');
    $this->assertDatabaseMissing('events', ['id' => $eventId]);
});

it('seeds twenty demo students and events without creating duplicates', function () {
    $this->seed(DemoEventStudentSeeder::class);
    $this->seed(DemoEventStudentSeeder::class);

    expect(User::query()->where('email', 'like', 'sinhvien%@campus.test')->count())->toBe(20)
        ->and(Event::query()->where('description', 'like', 'Tham gia % và kết nối với cộng đồng sinh viên.')->count())->toBe(20);
});

it('shows the requested news categories', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('announcements.index'))
        ->assertSee('Toàn bộ')
        ->assertSee('Sự kiện câu lạc bộ')
        ->assertSee('Sự kiện tình nguyện')
        ->assertSee('name="q"', false)
        ->assertSee('name="from"', false)
        ->assertSee('name="to"', false);
});

it('shows the club submenu and club application selector', function () {
    $user = User::factory()->create();

    $dashboard = $this->actingAs($user)->get(route('dashboard'));

    $dashboard->assertSee('Câu lạc bộ Sinh viên')
        ->assertSee(route('clubs.index'), false)
        ->assertSee(route('clubs.register'), false);

    $this->get(route('clubs.register'))
        ->assertSee('Đăng ký câu lạc bộ')
        ->assertSee('Câu lạc bộ')
        ->assertSee('name="club_id"', false)
        ->assertDontSee('name="desired_club"', false);
});

it('requires authentication to open student navigation destinations', function () {
    $this->get(route('events.calendar'))
        ->assertRedirect(route('login'));
});

it('shows only events for the selected calendar month', function () {
    Event::query()->create([
        'title' => 'Sự kiện tháng mười',
        'description' => 'Sự kiện trong tháng được chọn.',
        'location' => 'Hội trường A',
        'event_date' => '2026-10-15 09:00:00',
        'registration_deadline' => '2026-10-10 23:59:00',
        'max_participants' => 100,
        'status' => 'upcoming',
    ]);

    Event::query()->create([
        'title' => 'Sự kiện tháng mười một',
        'description' => 'Sự kiện ngoài tháng được chọn.',
        'location' => 'Hội trường B',
        'event_date' => '2026-11-15 09:00:00',
        'registration_deadline' => '2026-11-10 23:59:00',
        'max_participants' => 100,
        'status' => 'upcoming',
    ]);

    $response = $this->actingAs(User::factory()->create())
        ->get(route('events.calendar', ['month' => 10, 'year' => 2026]));

    $response->assertSee('Tháng 10 năm 2026')
        ->assertSee('Sự kiện tháng mười')
        ->assertDontSee('Sự kiện tháng mười một')
        ->assertSee('Tháng trước')
        ->assertSee('Tháng sau');
});

it('rejects an invalid event calendar month', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('dashboard'))
        ->get(route('events.calendar', ['month' => 13, 'year' => 2026]))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasErrors('month');
});

it('shows the dashboard date in the Vietnam timezone', function () {
    $this->travelTo(new DateTimeImmutable('2026-09-30T17:30:00+00:00'));

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSee('thứ năm, ngày 01/10/2026');
});
