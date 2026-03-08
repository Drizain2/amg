<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branche extends Model
{
    protected $table = "branches";
    protected $fillable = [
        "name",
        "address",
        "compagnie_id"
    ];

    public function companie()
    {
        return $this->belongsTo(Compagnie::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
