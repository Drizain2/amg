<?php

namespace App\Observers;

use App\Models\StockMovement;

class StockMovementObserver
{
   /**
     * Toute la logique de mise à jour du stock est ICI dans l'observer.
     * Les controllers ne touchent JAMAIS au stock directement.
     *
     * Types gérés :
     * - in          → incrémente le stock de la branche
     * - out         → décrémente le stock de la branche
     * - adjustment  → incrémente (correction positive ou remise à niveau)
     * - transfert   → décrémente le stock source, incrémente le stock destination
     *                 La convention : quantity > 0 = source, on lit to_stock_id dans la reason
     *                 Non — on utilise un champ dédié : voir note ci-dessous
     */
    public function created(StockMovement $movement): void
    {
        $stock = $movement->stock;

       match ($movement->type) {
            'in', 'adjustment' => $stock->increment('quantity', $movement->quantity),
            'out'              => $stock->decrement('quantity', $movement->quantity),

            // Le transfert crée DEUX mouvements distincts (out + in) via le controller
            // L'observer n'a donc rien de spécial à faire pour 'transfert' —
            // chaque mouvement est traité individuellement comme un out ou un in.
            // Le type 'transfert' sert uniquement à identifier l'origine dans l'historique.
            'transfert'        => null,

            default            => null,
        };
        
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
