<?php

namespace App\Policies;

use App\Models\EventRegistration;
use App\Models\User;

class EventRegistrationPolicy
{
    /**
     * Determine whether the user can view the registration.
     */
    public function view(User $user, EventRegistration $registration): bool
    {
        return $registration->user_id === $user->id
            || $registration->event->isLeader($user);
    }

    /**
     * Determine whether the user can manage the registration (approve/reject).
     */
    public function manage(User $user, EventRegistration $registration): bool
    {
        return $registration->event->isLeader($user);
    }

    /**
     * Determine whether the user can cancel their registration.
     */
    public function cancel(User $user, EventRegistration $registration): bool
    {
        return $registration->user_id === $user->id;
    }
}
