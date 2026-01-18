<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        // Filter by type
        if ($request->filled('type')) {
            if ($request->type === 'upcoming') {
                $query->where('StartDate', '>=', now());
            } elseif ($request->type === 'past') {
                $query->where('EndDate', '<', now());
            }
        } else {
            // Default: show upcoming and current events
            $query->where(function ($q) {
                $q->where('EndDate', '>=', now())
                  ->orWhereNull('EndDate');
            });
        }

        $events = $query->orderBy('StartDate', 'asc')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        // Get related events
        $relatedEvents = Event::where('ID', '!=', $event->ID)
            ->where('StartDate', '>=', now())
            ->orderBy('StartDate', 'asc')
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }

    public function ics(Event $event)
    {
        $startDate = $this->formatIcsDate($event->start_date, $event->start_time);
        $endDate = $this->formatIcsDate($event->end_date ?? $event->start_date, $event->end_time ?? $event->start_time);

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Pecos River Trading//Events//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:" . $event->ID . "@" . request()->getHost() . "\r\n";
        $ics .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
        $ics .= "DTSTART:" . $startDate . "\r\n";
        $ics .= "DTEND:" . $endDate . "\r\n";
        $ics .= "SUMMARY:" . $this->escapeIcs($event->title) . "\r\n";
        $ics .= "DESCRIPTION:\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        $filename = \Str::slug($event->title) . '.ics';

        return response($ics)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function formatIcsDate($date, $time = null): string
    {
        if (!$date) {
            return now()->format('Ymd');
        }
        if ($time) {
            return $date->format('Ymd') . 'T' . str_replace(':', '', $time) . '00';
        }
        return $date->format('Ymd');
    }

    private function escapeIcs(string $text): string
    {
        return str_replace(["\r\n", "\n", "\r", ",", ";", "\\"], ["\\n", "\\n", "\\n", "\\,", "\\;", "\\\\"], $text ?? '');
    }
}
