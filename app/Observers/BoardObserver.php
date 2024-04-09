<?php

namespace App\Observers;

use App\Models\Board;

class BoardObserver
{
    // перед сохранением записи
    public function saving(Board $board): void
    {
        $board->user_id = backpack_user()->id;
    }

    /**
     * Handle the Board "created" event.
     */
    public function created(Board $board): void
    {
        //
    }

    /**
     * Handle the Board "updated" event.
     */
    public function updated(Board $board): void
    {
        //
    }

    /**
     * Handle the Board "deleted" event.
     */
    public function deleted(Board $board): void
    {
        //
    }

    /**
     * Handle the Board "restored" event.
     */
    public function restored(Board $board): void
    {
        //
    }

    /**
     * Handle the Board "force deleted" event.
     */
    public function forceDeleted(Board $board): void
    {
        //
    }
}
