<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_company',
        'description_company',
        'picture_company',
        'zipcode',
        'phone',
        'address',
        'siret',
        'town',
        'lat',
        'long',
        'id_user'
    ];

    public function product()
    {
        return $this->belongsToMany(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}