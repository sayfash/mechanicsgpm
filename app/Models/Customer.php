<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'address',
        'id_card_number',
        'customer_status',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
