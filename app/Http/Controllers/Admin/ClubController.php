<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Club::class);

        $clubCount = Club::query()->count();
        $assignedLeaderCount = Club::query()->whereNotNull('leader_id')->count();
        $clubs = Club::query()
            ->with('leader')
            ->withCount('approvedMembers')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.clubs.index', compact('clubs', 'clubCount', 'assignedLeaderCount'));
    }

    public function show(Club $club): View
    {
        $this->authorize('view', $club);

        $club->load([
            'leader',
            'approvedMembers' => fn (BelongsToMany $query) => $query->orderBy('name'),
            'pendingApplications' => fn (BelongsToMany $query) => $query->orderBy('name'),
        ]);
        $clubLeaders = User::query()
            ->where('role', 'club_leader')
            ->orderBy('name')
            ->get();

        return view('admin.clubs.show', compact('club', 'clubLeaders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Club::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:clubs,name'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        Club::query()->create($validated);

        return redirect()->route('admin.clubs.index')->with('success', 'Câu lạc bộ đã được tạo.');
    }

    public function updateLeader(Request $request, Club $club): RedirectResponse
    {
        $this->authorize('update', $club);

        $validated = $request->validate([
            'leader_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', 'club_leader'),
            ],
        ]);

        $previousLeaderId = $club->leader_id;
        $newLeaderId = $validated['leader_id'] ?? null;

        DB::transaction(function () use ($club, $newLeaderId, $previousLeaderId): void {
            $club->update(['leader_id' => $newLeaderId]);

            if ($previousLeaderId !== null && $previousLeaderId !== $newLeaderId) {
                $previousLeader = User::query()->find($previousLeaderId);

                if ($previousLeader?->isClubLeader() && ! $previousLeader->managedClubs()->exists()) {
                    $previousLeader->forceFill(['role' => 'student'])->save();
                }
            }
        });

        return redirect()->route('admin.clubs.show', $club)->with('success', 'Trưởng câu lạc bộ đã được cập nhật.');
    }

    public function destroy(Club $club): RedirectResponse
    {
        $this->authorize('delete', $club);

        $leaderId = $club->leader_id;

        DB::transaction(function () use ($club, $leaderId): void {
            $club->delete();

            if ($leaderId !== null) {
                $leader = User::query()->find($leaderId);

                if ($leader?->isClubLeader() && ! $leader->managedClubs()->exists()) {
                    $leader->forceFill(['role' => 'student'])->save();
                }
            }
        });

        return redirect()->route('admin.clubs.index')->with('success', 'Câu lạc bộ đã được xóa.');
    }
}
