<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compagnie extends Model
{
    use SoftDeletes;
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
