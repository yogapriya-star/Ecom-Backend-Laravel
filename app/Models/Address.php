<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'address_line1', 'address_line2',
        'city', 'state', 'zipcode', 'order_notes', 'created_by'
    ];

    // Owner of the address
    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Users associated with this address (pivot)
    public function users()
    {
        return $this->belongsToMany(User::class, 'address_user', 'address_id', 'user_id')->withTimestamps();
    }
}
