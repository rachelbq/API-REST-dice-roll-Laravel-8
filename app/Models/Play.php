<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    use HasFactory;
        protected $fillable = [
        'dice1',
        'dice2',
        'sum',
        'result',
        'user_id',
    ];

    // many plays belong to 1 user
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
