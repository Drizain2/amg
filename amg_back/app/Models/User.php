<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        "last_name",
        "compagnie_id",
        "role",
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relations
     */
    public function compagnie()
    {
        return $this->belongsTo(Compagnie::class);
    }

    /**
     * Branches assignées via la table pivot branche_user.
     * Utilisé par manager et operator.
     * L'admin n'a pas de lignes ici — il accède à toutes les branches via compagnie_id.
     */
    public function branches()
    {
        return $this->belongsToMany(Branche::class, 'branche_user');
    }

    // ─── Helpers de rôle ─────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Retourne les IDs des branches accessibles selon le rôle.
     * - admin    → toutes les branches de sa compagnie
     * - manager  → ses branches assignées (pivot)
     * - operator → ses branches assignées (pivot, normalement 1 seule)
     */
    public function accessibleBrancheIds()
    {
        if ($this->isAdmin()) {
            return Branche::where('compagnie_id', $this->compagnie_id)
                ->pluck('id')
                ->toArray();
        }

        return $this->branches()->pluck('branches.id')->toArray();
    }

    /**
     * Vérifie si le user a accès à une branche donnée.
     */
    public function canAccessBranche(int $brancheId): bool
    {
        if ($this->isAdmin()) {
            // L'admin peut accéder à toute branche de sa compagnie
            return Branche::where('id', $brancheId)
                ->where('compagnie_id', $this->compagnie_id)
                ->exists();
        }

        return $this->branches()->where('branches.id', $brancheId)->exists();
    }
}
