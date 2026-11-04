<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabangs';
    protected $primaryKey = 'id_cab';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cab',
        'nama_cabang',
        'alamat',
        'nama_pic',
        'user_name_jne',
        'no_hp',
    ];

    public function packingLists(): HasMany
    {
        return $this->hasMany(PackingList::class, 'id_cab', 'id_cab');
    }

    public function operasionals(): HasMany
    {
        return $this->hasMany(Operasional::class, 'id_cab', 'id_cab');
    }

    public function komisis(): HasMany
    {
        return $this->hasMany(Komisi::class, 'id_cab', 'id_cab');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_cab', 'id_cab');
    }
}
