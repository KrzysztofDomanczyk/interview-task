<?php

namespace App\Policies;

use App\Invoice;
use App\Models\User::class;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User::class $user::class): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User::class $user::class, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User::class $user::class): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User::class $user::class, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User::class $user::class, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User::class $user::class, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User::class $user::class, Invoice $invoice): bool
    {
        //
    }
}
