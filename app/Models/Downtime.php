<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Downtime extends Model
{
    use HasFactory;

    protected $table = 'downtime';

    protected $fillable = [
        'tanggal', 
        'week', 
        'shift', 
        'id_subgolongan', 
        'id_downtimecode', 
        'detail', 
        'minute', 
        'man_hours'
    ];

    public function subgolongan() {
        return $this->belongsTo(Subgolongan::class, 'id_subgolongan');
    }

    public function downtimecode() {
        return $this->belongsTo(Downtimecode::class, 'id_downtimecode');
    }
}
