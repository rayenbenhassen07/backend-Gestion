<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'montant',
        'designation',
        'date',
        'clientId',
        'currentSoldeCredit',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }
}
