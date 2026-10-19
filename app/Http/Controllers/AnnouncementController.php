<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(private readonly AnnouncementService $announcementService)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'in:all,club-events,volunteer-events,general'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $announcements = Announcement::query()
            ->when(($filters['category'] ?? 'all') !== 'all', fn (Builder $query) => $query->where('category', $filters['category']))
            ->when($filters['q'] ?? null, fn (Builder $query, string $term) => $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', '%'.$term.'%')
                    ->orWhere('body', 'like', '%'.$term.'%');
            }))
            ->when($filters['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('published_at', '>=', $date))
            ->when($filters['to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('published_at', '<=', $date))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('student.announcements', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'category' => ['required', 'in:club-events,volunteer-events,general'],
        ]);

        $this->announcementService->create($validated);

        return redirect()->route('announcements.index')->with('success', 'Tin tức đã được đăng.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $this->announcementService->delete($announcement);

        return redirect()->route('announcements.index')->with('success', 'Tin tức đã được xóa.');
    }
}
