<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Targetdw extends Model
{
    use HasFactory;
    
    protected $table = 'targetdw';

    protected $fillable = [
        'month',
        'target'
    ];
}
