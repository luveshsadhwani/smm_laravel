<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $dates = ['expiry_date', 'entry_date'];

    protected $fillables = [
        'user_id', 
        'barcode',
        'item',
        'desc',
        'quantity',
        'expiry_date',
        'entry_date'
    ];

    protected $hidden = [
        'user_id',
        'notification_id',
        'created_at',
        'updated_at'
    ];
}
