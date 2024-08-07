<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldClientInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'credit',
        'accompte',
        'achat',
        'resteAPayer',
    ];
}