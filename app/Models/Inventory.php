<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $hidden = [
        'user_id',
        'item_id'
    ];

    protected $attributes = [
        'quantity' => 0
    ];

    public function item()
    {
        return $this->belongsTo(App\Model\Item::class, 'item_id', 'id');
    }
}
