<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('manageStudents', User::class);

        $roleCounts = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->whereIn('role', UserRole::values())
            ->groupBy('role')
            ->pluck('total', 'role');

        $studentsQuery = User::query()
            ->whereIn('role', [UserRole::Student->value, UserRole::ClubLeader->value])
            ->orderBy('name');

        $search = trim((string) $request->input('search', ''));
        $roleFilter = $request->input('role', 'all');

        if ($search !== '') {
            $studentsQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (in_array($roleFilter, [UserRole::Student->value, UserRole::ClubLeader->value], true)) {
            $studentsQuery->where('role', $roleFilter);
        }

        $students = $studentsQuery->paginate(20)->appends($request->query());

        return view('admin.students.index', [
            'students' => $students,
            'totalAccountCount' => User::query()->count(),
            'studentCount' => (int) ($roleCounts[UserRole::Student->value] ?? 0),
            'clubLeaderCount' => (int) ($roleCounts[UserRole::ClubLeader->value] ?? 0),
            'adminCount' => (int) ($roleCounts[UserRole::Admin->value] ?? 0),
            'search' => $search,
            'roleFilter' => in_array($roleFilter, ['all', UserRole::Student->value, UserRole::ClubLeader->value], true) ? $roleFilter : 'all',
        ]);
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $student = $user->load(['eventRegistrations.event']);

        return view('admin.students.show', compact('student'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::Student->value,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Tài khoản sinh viên mới đã được tạo.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        abort_unless(in_array($user->role, [UserRole::Student, UserRole::ClubLeader], true), 404);

        $validated = $request->validate([
            'role' => ['required', 'in:'.implode(',', [
                UserRole::Student->value,
                UserRole::ClubLeader->value,
            ])],
        ]);

        if ($validated['role'] !== UserRole::ClubLeader->value && $user->managedClubs()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'Hãy gán trưởng mới cho các câu lạc bộ trước khi đổi vai trò tài khoản này.',
            ]);
        }

        $user->forceFill([
            'role' => $validated['role'],
        ])->save();

        return redirect()->route('admin.students.index')->with('success', 'Vai trò tài khoản đã được cập nhật.');
    }

    public function updateRoles(Request $request): RedirectResponse
    {
        $this->authorize('manageStudents', User::class);

        $validated = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'in:'.implode(',', [
                UserRole::Student->value,
                UserRole::ClubLeader->value,
            ])],
        ]);

        $roleChanges = collect($validated['roles']);
        $leaderIds = User::query()
            ->whereIn('id', $roleChanges->keys())
            ->where('role', UserRole::ClubLeader->value)
            ->whereHas('managedClubs')
            ->pluck('id');

        if ($leaderIds->contains(fn ($leaderId): bool => $roleChanges->get($leaderId) !== UserRole::ClubLeader->value)) {
            throw ValidationException::withMessages([
                'roles' => 'Hãy gán trưởng mới cho các câu lạc bộ trước khi đổi vai trò tài khoản đó.',
            ]);
        }

        DB::transaction(function () use ($validated): void {
            foreach ($validated['roles'] as $studentId => $role) {
                User::query()
                    ->whereKey($studentId)
                    ->whereIn('role', [UserRole::Student->value, UserRole::ClubLeader->value])
                    ->update(['role' => $role]);
            }
        });

        return redirect()->route('admin.students.index')->with('success', 'Vai trò sinh viên trên trang đã được cập nhật.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        abort_unless(in_array($user->role, [UserRole::Student, UserRole::ClubLeader], true), 404);

        $user->delete();

        return redirect()->route('admin.students.index')->with('success', 'Tài khoản đã được xóa.');
    }
}
