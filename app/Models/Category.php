<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // The table associated with the model (optional if table follows Laravel's naming convention)
    protected $table = 'category';

    // The primary key associated with the table (optional if it's "id" by default)
    protected $primaryKey = 'CategoryID';

    // The attributes that are mass assignable (fields you can mass assign)
    protected $fillable = ['Name', 'Description'];

    // The attributes that should be hidden for arrays (optional)
    protected $hidden = [];

    // Timestamps are enabled by default (created_at, updated_at) so no need to set it unless disabled
    public $timestamps = true;

    // You can define any relationships or methods you want here
}
