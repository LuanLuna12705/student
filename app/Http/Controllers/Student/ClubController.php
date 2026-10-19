<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Services\ClubApplicationService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function __construct(private readonly ClubApplicationService $clubApplicationService)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Club::class);

        $user = auth()->user();

        if ($user->isClubLeader()) {
            $managedClubs = $user->managedClubs()
                ->withCount('approvedMembers')
                ->with([
                    'approvedMembers' => fn (BelongsToMany $query) => $query->orderBy('name'),
                    'pendingApplications' => fn (BelongsToMany $query) => $query->orderBy('name'),
                ])
                ->orderBy('name')
                ->get();

            return view('student.my-clubs', [
                'isClubLeader' => true,
                'managedClubs' => $managedClubs,
            ]);
        }

        $clubs = $user->clubs()->with('leader')->orderBy('name')->get();

        return view('student.my-clubs', [
            'isClubLeader' => false,
            'clubs' => $clubs,
        ]);
    }

    public function create(): View
    {
        $this->authorize('viewAny', Club::class);
        abort_unless(auth()->user()->isStudent() || auth()->user()->isAdmin() || auth()->user()->isClubLeader(), 403);

        $clubs = Club::query()->orderBy('name')->get();
        $activeApplications = auth()->user()->clubs()
            ->wherePivotIn('status', ['pending', 'approved'])
            ->pluck('clubs.id');
        $availableClubs = $clubs->reject(
            fn (Club $club): bool => $activeApplications->contains($club->id)
        );

        return view('student.club-register', compact('availableClubs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Club::class);
        abort_unless(auth()->user()->isStudent() || auth()->user()->isAdmin() || auth()->user()->isClubLeader(), 403);

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'phone' => ['required', 'string', 'max:32'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'gender' => ['required', 'in:Nam,Nữ,Khác'],
            'cohort' => ['nullable', 'in:K16,K17,K18,K19,K20,Sinh viên không thuộc ĐH Phenikaa'],
        ]);

        $club = Club::query()->findOrFail($validated['club_id']);
        $user = auth()->user();
        $existingMembership = $club->members()->whereKey($user->getKey())->first();

        if ($existingMembership?->pivot->status !== null && $existingMembership->pivot->status !== 'rejected') {
            return back()->withErrors([
                'club_id' => 'Bạn đã có đơn chờ duyệt hoặc là thành viên của câu lạc bộ này.',
            ])->withInput();
        }

        $this->clubApplicationService->apply($user, $club, $validated);

        return redirect()->route('clubs.index')->with('success', 'Đơn đăng ký đã được gửi đến trưởng câu lạc bộ.');
    }

    public function approve(Club $club, User $user): RedirectResponse
    {
        return $this->reviewApplication($club, $user, 'approved', 'Đơn đăng ký đã được duyệt.');
    }

    public function reject(Club $club, User $user): RedirectResponse
    {
        return $this->reviewApplication($club, $user, 'rejected', 'Đơn đăng ký đã bị từ chối.');
    }

    private function reviewApplication(Club $club, User $user, string $status, string $message): RedirectResponse
    {
        $this->authorize('reviewApplication', $club);

        $this->clubApplicationService->review($club, $user, $status);

        return redirect()->route('clubs.index')->with('success', $message);
    }
}
