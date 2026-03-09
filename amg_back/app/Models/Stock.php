<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "branche_id",
        "product_id",
        "quantity",
    ];

    public function branche()
    {
        return $this->belongsTo(Branche::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
