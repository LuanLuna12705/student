<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(private readonly EventRegistrationService $eventRegistrationService)
    {
    }

    public function index(Request $request): View
    {
        $query = Event::query()
            ->withCount('registeredUsers')
            ->orderBy('event_date');

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'all');

        if ($search !== '') {
            $query->where(function ($eventQuery) use ($search): void {
                $eventQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status === 'open') {
            $now = now();
            $query->where('registration_deadline', '>=', $now)
                ->where('event_date', '>=', $now);
        }

        if ($status === 'closed') {
            $query->where(function ($eventQuery): void {
                $eventQuery->where('registration_deadline', '<', now())
                    ->orWhere('event_date', '<', now());
            });
        }

        $events = $query->paginate(12)->appends($request->query());

        return view('student.events.index', [
            'events' => $events,
            'search' => $search,
            'status' => in_array($status, ['all', 'open', 'closed'], true) ? $status : 'all',
        ]);
    }

    public function joined(): View
    {
        $events = auth()->user()
            ->registeredEvents()
            ->withCount('registeredUsers')
            ->orderBy('event_date')
            ->get();

        return view('student.events.joined', compact('events'));
    }

    public function register(Event $event): RedirectResponse
    {
        $this->authorize('register', $event);

        $result = $this->eventRegistrationService->register(auth()->user(), $event);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('events.joined')->with('success', $result['message']);
    }

    public function unregister(Event $event): RedirectResponse
    {
        $this->authorize('unregister', $event);

        $this->eventRegistrationService->unregister(auth()->user(), $event);

        return back()->with('success', 'Bạn đã hủy đăng ký sự kiện thành công.');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'location' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after:now'],
            'registration_deadline' => ['required', 'date', 'after:now', 'before:event_date'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $data = [
            ...$validated,
            'status' => 'upcoming',
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::disk('public')->url(
                $request->file('image')->store('event-images', 'public')
            );
        }

        unset($data['image']);

        Event::query()->create($data);

        return redirect()->route('events.index')->with('success', 'Sự kiện đã được thêm.');
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'location' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after:now'],
            'registration_deadline' => ['required', 'date', 'after:now', 'before:event_date'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::disk('public')->url(
                $request->file('image')->store('event-images', 'public')
            );
        }

        unset($data['image']);

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Sự kiện đã được cập nhật.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Sự kiện đã được xóa.');
    }
}
