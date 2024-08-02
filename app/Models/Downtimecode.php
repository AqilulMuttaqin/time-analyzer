<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Downtimecode extends Model
{
    use HasFactory;

    protected $table = 'downtimecode';

    protected $fillable = [
        'kode',
        'keterangan',
        'id_section'
    ];

    public function section() {
        return $this->belongsTo(Section::class, 'id_section');
    }

    public function downtime() {
        return $this->hasMany(Downtime::class, 'id_downtimecode');
    }
}
