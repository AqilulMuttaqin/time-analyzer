<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Effective extends Model
{
    use HasFactory;

    protected $table = 'effective';

    protected $fillable = [
        'tanggal',
        'week', 
        'shift', 
        'standart', 
        'indirect', 
        'overtime', 
        'reguler_eh',
        'id_subgolongan'
    ];

    public function subgolongan() {
        return $this->belongsTo(Subgolongan::class, 'id_subgolongan');
    }
}
