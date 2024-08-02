<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Golongan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'golongan';

    protected $fillable = [
        'nama',
        'lokasi'
    ];

    public function subgolongan() {
        return $this->hasMany(Subgolongan::class, 'id_golongan');
    }
}
