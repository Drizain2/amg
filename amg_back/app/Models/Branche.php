<?php

namespace App\Models;

use App\Models\Scopes\CompagnieScope;
use Illuminate\Database\Eloquent\Model;

class Branche extends Model
{
    protected $table = "branches";
    protected $fillable = [
        "name",
        "address",
        "compagnie_id"
    ];

    /**
     * Relations
     */
    public function companie()
    {
        return $this->belongsTo(Compagnie::class);
    }
    /**
     * Users assignés à cette branche via pivot.
     * Ne concerne pas les admins (accès direct via compagnie_id).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'branche_user');
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompagnieScope);
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->compagnie_id = auth()->user()->compagnie_id;
            }
        });
    }
}
