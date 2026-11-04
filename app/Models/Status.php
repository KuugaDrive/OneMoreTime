<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'statuses';
    protected $primaryKey = 'id_status';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_status',
        'nama_status',
        'level',
        'ket_status',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'status_id', 'id_status');
    }
}
