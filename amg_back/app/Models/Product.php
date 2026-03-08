<?php

namespace App\Models;

use App\Models\Scopes\CompagnieScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "name",
        "compagnie_id",
        "sku",
        "price"
    ];

    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    protected static function booted(){
        static::addGlobalScope(new CompagnieScope);
        static::creating(function ($model) {
        if (auth()->check()) {
            $model->compagnie_id = auth()->user()->compagnie_id;
        }
    });
    }
}
