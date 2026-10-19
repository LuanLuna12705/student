<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'between:1,9999'],
        ]);

        $currentDate = now();
        $month = (int) ($validated['month'] ?? $currentDate->month);
        $year = (int) ($validated['year'] ?? $currentDate->year);
        $monthStart = CarbonImmutable::create($year, $month, 1, 0, 0, 0, config('app.timezone'));
        $monthEnd = $monthStart->endOfMonth();
        $calendarStart = $monthStart->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->endOfWeek(Carbon::SUNDAY);
        $calendarDays = [];

        for ($day = $calendarStart; $day->lte($calendarEnd); $day = $day->addDay()) {
            $calendarDays[] = $day;
        }

        $events = Event::query()
            ->whereBetween('event_date', [$monthStart, $monthEnd])
            ->orderBy('event_date')
            ->get();

        return view('student.event-calendar', [
            'calendarDays' => $calendarDays,
            'eventsByDate' => $events->groupBy(fn (Event $event): string => $event->event_date->toDateString()),
            'monthLabel' => $monthStart->locale('vi')->isoFormat('[Tháng] M [năm] YYYY'),
            'monthStart' => $monthStart,
            'nextMonth' => $monthStart->addMonth(),
            'previousMonth' => $monthStart->subMonth(),
        ]);
    }
}
