<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'num',
        'gredit',
        'oldCredit',
        'designation',
        'date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'clientId');
    }
}
