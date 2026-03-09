<?php

namespace App\Models;

use App\Models\Scopes\CompagnieScope;
use App\Models\Traits\BelongsToCompagnie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branche extends Model
{
    use SoftDeletes,BelongsToCompagnie;
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

}
