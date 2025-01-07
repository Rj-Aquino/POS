<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'TransactionID'; // Set the primary key
    protected $fillable = [
        'OrderID',
        'LoyaltyCardID',
        'TotalPointsUsed',
        'PointsEarned',
        'TransactionDate',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }

}
