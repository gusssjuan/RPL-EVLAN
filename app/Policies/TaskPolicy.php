<?php

namespace App\Policies;

use App\Enums\StatusEvent;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return $task->event->isLeader($user) || $task->event->isMember($user);
    }

    /**
     * Determine whether the user can create tasks for an event.
     */
    public function create(User $user, Event $event): bool
    {
        return $event->isLeader($user) && $event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can update the task details.
     */
    public function update(User $user, Task $task): bool
    {
        return $task->event->isLeader($user) && $task->event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->event->isLeader($user) && $task->event->status_event !== StatusEvent::Selesai;
    }

    /**
     * Determine whether the user can update the task status (only the assignee / penerima).
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $task->penerima_id === $user->id && $task->event->status_event !== StatusEvent::Selesai;
    }
}
