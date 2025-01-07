<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'OrderID';

    // If the primary key is not auto-incrementing (optional)
    public $incrementing = true;

    // Set the data type for the primary key (optional)
    protected $keyType = 'int';

    // If you want to fill in fields (mass-assignment)
    protected $fillable = [
        'OrderDate',
        'Subtotal',
        'Total'
    ];

    // Define relationships (if any)
    // For example, if you have a Customer model

}


