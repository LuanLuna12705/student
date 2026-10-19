<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtracurricularScoreController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        $eventsBySemester = $student->registeredEvents()
            ->wherePivot('status', 'registered')
            ->orderBy('event_date')
            ->get()
            ->groupBy(function (Event $event): string {
                $month = $event->event_date->month;
                $academicYearStart = $month >= 9 ? $event->event_date->year : $event->event_date->year - 1;
                $semester = match (true) {
                    $month >= 9 || $month <= 1 => 'Học kỳ I',
                    $month <= 6 => 'Học kỳ II',
                    default => 'Học kỳ hè',
                };

                return sprintf('%s %d-%d', $semester, $academicYearStart, $academicYearStart + 1);
            });

        return view('student.extracurricular-scores', compact('student', 'eventsBySemester'));
    }
}
