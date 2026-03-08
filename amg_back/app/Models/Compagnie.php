<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compagnie extends Model
{
    protected $fillable = [
        "name",
        "slug",
        "email",
        "phone"
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branche::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
