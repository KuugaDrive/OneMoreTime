<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komisi extends Model
{
    protected $table = 'komisis';
    protected $primaryKey = 'kode_trx_cl';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_trx_cl',
        'id_cab',
        'tanggal_tf',
        'nominal',
        'ket_trx_cl',
        'created_by',
    ];

    protected $casts = [
        'tanggal_tf' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'id_cab', 'id_cab');
    }

    public function getNamaCabangAttribute(): string
    {
        return $this->cabang ? $this->cabang->nama_cabang : '-';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
