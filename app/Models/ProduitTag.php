<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'id-product',
        'id_tag',
    ];
    public function product()
    {
        return $this->belongsToMany(Product::class);
    }
    public function tag()
    {
        return $this->belongsToMany(Tag::class);
    }
}
