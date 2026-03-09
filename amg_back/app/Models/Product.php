<?php

namespace App\Models;

use App\Models\Scopes\CompagnieScope;
use App\Models\Traits\BelongsToCompagnie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes,BelongsToCompagnie;
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

}
