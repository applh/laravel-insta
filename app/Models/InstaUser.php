<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstaUser extends Model
{
    use HasFactory;

    // fillable
    protected $fillable = [
        'user_id',
        'insta_id',
        'insta_username',
        'access_token',
        'access_token_expires_in',
    ];
}
