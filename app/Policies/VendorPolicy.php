<?php

namespace App\Policies;

use App\Enums\StatusVerifikasi;
use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    /**
     * Determine whether anyone can view the vendor.
     */
    public function view(?User $user, Vendor $vendor): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the vendor profile.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        return $vendor->user_id === $user->id;
    }

    /**
     * Determine whether the vendor can manage products/services.
     * Locked until status_verifikasi is Terverifikasi.
     */
    public function manageProducts(User $user, Vendor $vendor): bool
    {
        return $vendor->user_id === $user->id
            && $vendor->status_verifikasi === StatusVerifikasi::Terverifikasi;
    }
}

