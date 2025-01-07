<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $table = 'order_products'; // Explicitly specify the table name

    protected $fillable = [
        'OrderID',
        'ProductID',
        'Quantity',
        'TotalPrice',
    ];

    public $timestamps = false; // Disable timestamps for pivot table
}
