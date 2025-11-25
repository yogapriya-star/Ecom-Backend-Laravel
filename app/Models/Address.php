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
        'city', 'state', 'zipcode', 'order_notes', 'created_by','is_active'
    ];

    /**
     * Each address belongs to one user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
