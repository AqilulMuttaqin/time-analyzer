<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';

    protected $fillable = [
        'month',
        'id_section'
    ];

    public function section() {
        return $this->belongsTo(Section::class, 'id_section');
    }

    public function concern() {
        return $this->hasMany(Concern::class, 'id_report');
    }
}
