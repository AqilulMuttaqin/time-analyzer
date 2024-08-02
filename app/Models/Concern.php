<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    use HasFactory;

    protected $table = 'concern';

    protected $fillable = [
        'concerns',
        'id_report'
    ];

    public function report() {
        return $this->belongsTo(Report::class, 'id_report');
    }

    public function action() {
        return $this->hasMany(Action::class, 'id_concern');
    }
}
