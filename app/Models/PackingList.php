<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingList extends Model
{
    protected $table = 'packing_lists';
    protected $primaryKey = 'kode_trx_pl';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_trx_pl',
        'no_pl_jne',
        'id_cab',
        'tanggal_pl',
        'total',
        'discount_charge',
        'e_voucher',
        'grand_total',
        'jumlah_paket',
        'ket_trx_pl',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pl' => 'date',
        'total' => 'decimal:2',
        'discount_charge' => 'decimal:2',
        'e_voucher' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'jumlah_paket' => 'integer',
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
