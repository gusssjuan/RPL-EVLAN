<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $eventsLed = $user->eventsLed()->with('category')->latest()->get();
        $eventsJoined = $user->eventsJoined()->with('category')->latest()->get();

        return view('acara.index', compact('eventsLed', 'eventsJoined'));
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load(['category', 'leader', 'checklists', 'timelines' => function ($q) {
            $q->orderBy('offset_hari', 'desc');
        }, 'divisions.members.user', 'tasks']);

        return view('acara.show', compact('event'));
    }
}

