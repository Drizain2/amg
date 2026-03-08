<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        "branche_id",
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
    public function branche()
    {
        return $this->belongsTo(Branche::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
