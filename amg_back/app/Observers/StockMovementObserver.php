<?php

namespace App\Observers;

use App\Models\StockMovement;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $movement): void
    {
        $stock = $movement->stock;

        // On ajuste la quantité au stock selon le mouvement
        // Si in on ajoute
        // Si out on soustrait
        if ($movement->type === "in" || $movement->type === 'adjustment') {
            $stock->increment('quantity', $movement->quantity);
        } elseif ($movement->type === "out") {
            $stock->decrement('quantity', abs($movement->quantity));
        }
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }
}
