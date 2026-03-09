<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "stock_id",
        "reason",
        "type",
        "reference",
        "quantity",
        "user_id"
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Génère une référence unique pour le mouvement.
     * Format : MVT-YYYYMMDD-XXXXXX (ex: MVT-20260308-A3F9K2)
     *
     * On utilise strtoupper(substr(uniqid(), -6)) pour la partie aléatoire
     * plutôt qu'un simple rand() — meilleure unicité sur des insertions rapprochées.
     */
    public static function generateReference(): string
    {
        return 'MVT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
