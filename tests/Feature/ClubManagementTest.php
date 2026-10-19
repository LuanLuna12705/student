<?php

use App\Enums\UserRole;
use App\Models\Club;
use App\Models\User;

it('shows admin club-management tabs and lets admins create a club', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.students.index'))
        ->assertSee('Danh sách sinh viên')
        ->assertSee(route('admin.clubs.index'), false)
        ->assertSee('Quản lý sinh viên')
        ->assertSee('Danh sách câu lạc bộ');

    $this->actingAs($admin)
        ->get(route('admin.clubs.index'))
        ->assertSee('Danh sách câu lạc bộ')
        ->assertSee('Tên khởi tạo chỉ mang tính tham khảo')
        ->assertSee('Tổng số câu lạc bộ')
        ->assertSee('data-stat-value="0"', false);

    $this->actingAs($admin)
        ->post(route('admin.clubs.store'), [
            'name' => 'CLB kiểm thử',
            'description' => 'Mô tả câu lạc bộ kiểm thử.',
        ])
        ->assertRedirect(route('admin.clubs.index'));

    $this->assertDatabaseHas('clubs', [
        'name' => 'CLB kiểm thử',
        'description' => 'Mô tả câu lạc bộ kiểm thử.',
    ]);
});

it('lets admins assign a club leader and view approved members and pending applications', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $leader = User::factory()->create(['name' => 'Trưởng CLB', 'role' => 'student']);
    $member = User::factory()->create(['name' => 'Thành viên CLB']);
    $applicant = User::factory()->create(['name' => 'Sinh viên chờ duyệt']);
    $club = Club::query()->create(['name' => 'CLB kiểm thử']);

    $this->actingAs($admin)
        ->patch(route('admin.students.role', $leader), ['role' => 'club_leader'])
        ->assertRedirect(route('admin.students.index'));

    $this->actingAs($admin)
        ->patch(route('admin.clubs.leader', $club), ['leader_id' => $leader->id])
        ->assertRedirect(route('admin.clubs.show', $club));

    $this->actingAs($admin)
        ->get(route('admin.students.show', $leader))
        ->assertSee('Trưởng CLB');

    $club->members()->attach($member->id, ['status' => 'approved']);
    $club->members()->attach($applicant->id, ['status' => 'pending']);

    $this->actingAs($admin)
        ->get(route('admin.clubs.show', $club))
        ->assertSee('Trưởng CLB')
        ->assertSee('Thành viên CLB')
        ->assertSee('Sinh viên chờ duyệt')
        ->assertSee('Thành viên đã duyệt (1)')
        ->assertSee('Đơn đang chờ (1)');
});

it('lets students apply and only the assigned club leader can review the application', function () {
    $leader = User::factory()->create(['role' => 'club_leader']);
    $otherLeader = User::factory()->create(['role' => 'club_leader']);
    $student = User::factory()->create(['name' => 'Sinh viên đăng ký', 'role' => 'student']);
    $secondStudent = User::factory()->create(['name' => 'Sinh viên thứ hai', 'role' => 'student']);
    $club = Club::query()->create([
        'name' => 'CLB công nghệ kiểm thử',
        'leader_id' => $leader->id,
    ]);

    $application = [
        'club_id' => $club->id,
        'phone' => '0900123456',
        'facebook_url' => 'https://facebook.com/student.test',
        'gender' => 'Khác',
        'cohort' => 'K19',
    ];

    $this->actingAs($student)
        ->post(route('clubs.register.store'), $application)
        ->assertRedirect(route('clubs.index'));

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $student->id,
        'status' => 'pending',
        'phone' => '0900123456',
    ]);

    $this->actingAs($student)
        ->get(route('clubs.index'))
        ->assertSee('CLB công nghệ kiểm thử')
        ->assertSee('Chờ duyệt');

    $this->actingAs($otherLeader)
        ->post(route('clubs.applications.approve', [$club, $student]))
        ->assertForbidden();

    $this->actingAs($leader)
        ->get(route('clubs.index'))
        ->assertSee('Sinh viên đăng ký')
        ->assertSee('Duyệt');

    $this->actingAs($leader)
        ->post(route('clubs.applications.approve', [$club, $student]))
        ->assertRedirect(route('clubs.index'));

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $student->id,
        'status' => 'approved',
    ]);

    $this->actingAs($secondStudent)
        ->post(route('clubs.register.store'), [
            ...$application,
            'club_id' => $club->id,
        ])
        ->assertRedirect(route('clubs.index'));

    $this->actingAs($leader)
        ->post(route('clubs.applications.reject', [$club, $secondStudent]))
        ->assertRedirect(route('clubs.index'));

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $secondStudent->id,
        'status' => 'rejected',
    ]);

    $this->actingAs($secondStudent)
        ->post(route('clubs.register.store'), [
            ...$application,
            'club_id' => $club->id,
        ])
        ->assertRedirect(route('clubs.index'));

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $secondStudent->id,
        'status' => 'pending',
    ]);
});

it('prevents duplicate club applications and blocks students from reviewing applications', function () {
    $student = User::factory()->create(['role' => 'student']);
    $club = Club::query()->create(['name' => 'CLB duy nhất']);
    $club->members()->attach($student->id, ['status' => 'pending']);

    $this->actingAs($student)
        ->post(route('clubs.register.store'), [
            'club_id' => $club->id,
            'phone' => '0900123456',
            'gender' => 'Nam',
        ])
        ->assertSessionHasErrors('club_id');

    $this->actingAs($student)
        ->post(route('clubs.applications.approve', [$club, $student]))
        ->assertForbidden();
});

it('protects club administration from non-admin users', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('admin.clubs.index'))
        ->assertForbidden();
});

it('enforces policy-based authorization for club leadership and event registration', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $leader = User::factory()->create(['role' => 'club_leader']);
    $student = User::factory()->create(['role' => 'student']);
    $club = Club::query()->create([
        'name' => 'CLB chính thức',
        'leader_id' => $leader->id,
    ]);
    $event = \App\Models\Event::query()->create([
        'title' => 'Sự kiện kiểm thử',
        'description' => 'Mô tả sự kiện kiểm thử',
        'location' => 'Phòng 101',
        'event_date' => now()->addDays(3),
        'registration_deadline' => now()->addDay(),
        'max_participants' => 10,
        'status' => 'upcoming',
    ]);

    expect($admin->can('manageStudents', User::class))->toBeTrue();
    expect($student->can('manageStudents', User::class))->toBeFalse();
    expect($leader->can('reviewApplication', $club))->toBeTrue();
    expect($student->can('reviewApplication', $club))->toBeFalse();
    expect($leader->can('register', $event))->toBeTrue();
    expect($admin->can('register', $event))->toBeFalse();
});

it('shows accurate account and club dashboard counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->count(2)->create(['role' => 'student']);
    User::factory()->create(['role' => 'club_leader']);

    $clubWithLeader = Club::query()->create([
        'name' => 'CLB có trưởng',
        'leader_id' => User::query()->where('role', 'club_leader')->value('id'),
    ]);
    Club::query()->create(['name' => 'CLB chưa có trưởng']);

    $this->actingAs($admin)
        ->get(route('admin.students.index'))
        ->assertSee('Tài khoản sinh viên')
        ->assertSee('Trưởng câu lạc bộ')
        ->assertSee('Tài khoản quản trị')
        ->assertSee('data-stat-value="2"', false)
        ->assertSee('data-stat-value="1"', false);

    $this->actingAs($admin)
        ->get(route('admin.clubs.index'))
        ->assertSee('data-stat-value="2"', false)
        ->assertSee('data-stat-value="1"', false)
        ->assertSee($clubWithLeader->name);
});

it('prevents demoting a user who is still assigned as a club leader', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $leader = User::factory()->create(['role' => 'club_leader']);
    $club = Club::query()->create([
        'name' => 'CLB đang có trưởng',
        'leader_id' => $leader->id,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.students.role', $leader), ['role' => 'student'])
        ->assertSessionHasErrors('role');

    expect($leader->fresh()->role->value)->toBe(UserRole::ClubLeader->value);

    $this->actingAs($admin)
        ->patch(route('admin.clubs.leader', $club), ['leader_id' => null])
        ->assertRedirect(route('admin.clubs.show', $club));

    expect($leader->fresh()->role->value)->toBe(UserRole::Student->value);
});

it('allows admins to access club registration and apply for a club', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $club = Club::query()->create(['name' => 'CLB dành cho admin test']);

    $this->actingAs($admin)
        ->get(route('clubs.register'))
        ->assertOk()
        ->assertSee('CLB dành cho admin test')
        ->assertSee('Người đăng ký');

    $this->actingAs($admin)
        ->post(route('clubs.register.store'), [
            'club_id' => $club->id,
            'phone' => '0987654321',
            'gender' => 'Nam',
            'cohort' => 'K18',
        ])
        ->assertRedirect(route('clubs.index'));

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $admin->id,
        'status' => 'pending',
    ]);
});

