<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $table = 'action';

    protected $fillable = [
        'action',
        'pic',
        'due_date',
        'id_concern',
        'status'
    ];

    public function concern() {
        return $this->belongsTo(Concern::class, 'id_concern');
    }
}
