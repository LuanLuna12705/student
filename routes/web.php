<?php

use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\ClubController as AdminClubController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Student\ClubController as StudentClubController;
use App\Http\Controllers\Student\EventCalendarController;
use App\Http\Controllers\Student\ExtracurricularScoreController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $now = now();
    $upcomingEvents = Event::query()
        ->where('registration_deadline', '>=', $now)
        ->where('event_date', '>=', $now)
        ->withCount('registeredUsers')
        ->orderBy('event_date')
        ->limit(3)
        ->get();

    return view('dashboard', compact('upcomingEvents'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/search', [SearchController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('search.index');

Route::get('/announcements', [AnnouncementController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('announcements.index');

Route::view('/activities', 'student.section', [
    'title' => 'Góc hoạt động',
    'description' => 'Khám phá các hoạt động học tập, phong trào và trải nghiệm dành cho sinh viên.',
    'icon' => '🎓',
])->middleware(['auth', 'verified'])->name('activities.index');

Route::get('/activities/extracurricular-scores', [ExtracurricularScoreController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('activities.scores');

Route::get('/events', [EventController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('events.index');

Route::get('/events/joined', [EventController::class, 'joined'])
    ->middleware(['auth', 'verified'])
    ->name('events.joined');

Route::post('/events/{event}/register', [EventController::class, 'register'])
    ->middleware(['auth', 'verified'])
    ->name('events.register');

Route::delete('/events/{event}/register', [EventController::class, 'unregister'])
    ->middleware(['auth', 'verified'])
    ->name('events.unregister');

Route::get('/events/calendar', [EventCalendarController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('events.calendar');

Route::get('/clubs', [StudentClubController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('clubs.index');

Route::get('/clubs/register', [StudentClubController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('clubs.register');

Route::post('/clubs/register', [StudentClubController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('clubs.register.store');

Route::post('/clubs/{club}/applications/{user}/approve', [StudentClubController::class, 'approve'])
    ->middleware(['auth', 'verified', 'can:manage-clubs'])
    ->name('clubs.applications.approve');

Route::post('/clubs/{club}/applications/{user}/reject', [StudentClubController::class, 'reject'])
    ->middleware(['auth', 'verified', 'can:manage-clubs'])
    ->name('clubs.applications.reject');

Route::middleware(['auth', 'verified', 'can:manage-students'])->group(function () {
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/admin/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::post('/admin/students', [StudentController::class, 'store'])->name('admin.students.store');
    Route::get('/admin/students/{user}', [StudentController::class, 'show'])->name('admin.students.show');
    Route::patch('/admin/students/roles', [StudentController::class, 'updateRoles'])->name('admin.students.roles');
    Route::patch('/admin/students/{user}/role', [StudentController::class, 'updateRole'])->name('admin.students.role');
    Route::delete('/admin/students/{user}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    Route::get('/admin/administrators', [AdministratorController::class, 'index'])->name('admin.administrators.index');
    Route::post('/admin/administrators', [AdministratorController::class, 'store'])->name('admin.administrators.store');
    Route::get('/admin/clubs', [AdminClubController::class, 'index'])->name('admin.clubs.index');
    Route::post('/admin/clubs', [AdminClubController::class, 'store'])->name('admin.clubs.store');
    Route::get('/admin/clubs/{club}', [AdminClubController::class, 'show'])->name('admin.clubs.show');
    Route::patch('/admin/clubs/{club}/leader', [AdminClubController::class, 'updateLeader'])->name('admin.clubs.leader');
    Route::delete('/admin/clubs/{club}', [AdminClubController::class, 'destroy'])->name('admin.clubs.destroy');
});

Route::get('/my-events', [EventController::class, 'joined'])
    ->middleware(['auth', 'verified'])
    ->name('my-events.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
