<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subgolongan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subgolongan';

    protected $fillable = [
        'nama',
        'id_golongan'
    ];

    public function golongan() {
        return $this->belongsTo(Golongan::class, 'id_golongan');
    }

    public function downtime() {
        return $this->hasMany(Downtime::class, 'id_subgolongan');
    }

    public function effective() {
        return $this->hasMany(Effective::class, 'id_subgolongan');
    }
}
