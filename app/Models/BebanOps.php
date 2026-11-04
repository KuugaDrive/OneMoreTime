<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BebanOps extends Model
{
    protected $table = 'beban_ops';
    protected $primaryKey = 'id_ops';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ops',
        'beban_ops',
        'ket_ops',
    ];

    public function operasionals(): HasMany
    {
        return $this->hasMany(Operasional::class, 'beban_ops_id', 'id_ops');
    }
}
