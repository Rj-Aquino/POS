<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // The table associated with the model (optional if table follows Laravel's naming convention)
    protected $table = 'product';

    // The primary key associated with the table (optional if it's "id" by default)
    protected $primaryKey = 'ProductID';

    // The attributes that are mass assignable
    protected $fillable = ['CategoryID', 'Name', 'Price'];

    // The attributes that should be hidden for arrays (optional)
    protected $hidden = [];

    // Timestamps are enabled by default (created_at, updated_at) so no need to set it unless disabled
    public $timestamps = true;

    /**
     * Get the category that owns the product.
     */

        public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products', 'ProductID', 'OrderID')
                    ->withPivot('Quantity', 'TotalPrice');
    }

}
