<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdministratorController extends Controller
{
    public function index(): View
    {
        $this->authorize('manageStudents', User::class);

        $administrators = User::query()
            ->where('role', UserRole::Admin->value)
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.administrators.index', compact('administrators'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manageStudents', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::query()->create([
            ...$validated,
            'role' => UserRole::Admin->value,
        ]);

        return redirect()->route('admin.administrators.index')
            ->with('success', 'Tài khoản quản trị mới đã được tạo.');
    }
}
