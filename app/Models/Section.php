<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'section';

    protected $fillable = [
        'nama'
    ];

    public function downtimecode() {
        return $this->hasMany(Downtimecode::class, 'id_section');
    }

    public function users() {
        return $this->hasMany(User::class, 'id_section');
    }
}
