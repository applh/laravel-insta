<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstaMedia extends Model
{
    use HasFactory;
    
    // fillable
    protected $fillable = [
        'insta_user_id',
        'insta_media_username',
        'insta_media_id',
        'insta_media_type',
        'insta_media_url',
        'insta_media_caption',
        'insta_media_permalink',
        'insta_media_timestamp',
    ];
}
