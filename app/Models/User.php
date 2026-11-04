<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'username',
        'no_hp',
        'alamat',
        'status_id',
        'role',
        'id_cab',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id_status');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'id_cab', 'id_cab');
    }

    public function packingLists(): HasMany
    {
        return $this->hasMany(PackingList::class, 'created_by', 'id_user');
    }

    public function operasionals(): HasMany
    {
        return $this->hasMany(Operasional::class, 'created_by', 'id_user');
    }

    public function komisis(): HasMany
    {
        return $this->hasMany(Komisi::class, 'created_by', 'id_user');
    }

    public function isOwner(): bool
    {
        return strtolower($this->role) === 'owner';
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }
}
