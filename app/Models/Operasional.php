<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operasional extends Model
{
    protected $table = 'operasionals';
    protected $primaryKey = 'kode_trx_ops';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_trx_ops',
        'id_cab',
        'tanggal_trx_ops',
        'beban_ops_id',
        'nominal',
        'ket_trx_ops',
        'created_by',
    ];

    protected $casts = [
        'tanggal_trx_ops' => 'date',
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

    public function bebanOps(): BelongsTo
    {
        return $this->belongsTo(BebanOps::class, 'beban_ops_id', 'id_ops');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
