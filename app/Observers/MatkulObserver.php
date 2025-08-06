<?php

namespace App\Observers;

use App\Models\Matkul;

class MatkulObserver
{
    /**
     * Handle the Matkul "created" event.
     */
    public function created(Matkul $matkul): void
    {
        //
    }

    /**
     * Handle the Matkul "updated" event.
     */
    public function updated(Matkul $matkul): void
    {
        //
    }

    /**
     * Handle the Matkul "deleted" event.
     */
    public function deleted(Matkul $matkul): void
    {
        //
    }

    /**
     * Handle the Matkul "restored" event.
     */
    public function restored(Matkul $matkul): void
    {
        //
    }

    /**
     * Handle the Matkul "force deleted" event.
     */
    public function forceDeleted(Matkul $matkul): void
    {
        //
    }
}
