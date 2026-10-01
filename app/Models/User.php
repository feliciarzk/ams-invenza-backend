<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable([
    'role_id',
    'location_id',
    'name',
    'position',
    'email',
    'password',
    'is_active',
])]
#[Hidden([
    'password',
])]
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected function casts(): array
    {
        return [
            'role_id' => 'integer',
            'location_id' => 'integer',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Convert old bcrypt $2a$ hashes to $2y$
     * so Laravel can verify existing passwords.
     */
    public function getAuthPassword(): string
    {
        return str_replace('$2a$', '$2y$', $this->password);
    }

    /**
     * JWT identifier.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Custom JWT claims.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}