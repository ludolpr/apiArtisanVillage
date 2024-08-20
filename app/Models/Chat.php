<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_chat',
        'created_date',
        'id_user'
    ];
    public function user()
    {
        return $this->belongsTo(user::class);
    }
    public function message()
    {
        return $this->hasMany(Message::class);
    }
}
