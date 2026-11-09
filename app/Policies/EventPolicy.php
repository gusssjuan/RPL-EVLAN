<?php

namespace App\Policies;

use App\Enums\StatusEvent;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        if ($event->is_publik) {
            return true;
        }

        return $event->isLeader($user) || $event->isMember($user);
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        return $event->isLeader($user) && $event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $event->isLeader($user) && $event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can manage the event (invite members, add divisions, etc.).
     */
    public function manage(User $user, Event $event): bool
    {
        return $event->isLeader($user) && $event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can close and evaluate the event.
     */
    public function close(User $user, Event $event): bool
    {
        return $event->isLeader($user) && $event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can view event monitoring (D-16: Leader only).
     */
    public function viewMonitoring(User $user, Event $event): bool
    {
        return $event->isLeader($user);
    }

    /**
     * Determine whether the user can view event budget (D-16: Leader only).
     */
    public function viewBudget(User $user, Event $event): bool
    {
        return $event->isLeader($user);
    }
}
