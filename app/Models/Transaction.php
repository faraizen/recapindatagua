<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'title',
        'pemasukan',
        'pengeluaran',
        'date',
        'description',
        'pegangan',
        'save',
    ];

    protected $casts = [
        'date' => 'date',
        'pemasukan' => 'integer',
        'pengeluaran' => 'integer',
    ];
}
