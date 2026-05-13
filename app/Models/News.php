<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'content',
        'upload_time',
        'image',
        'source',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('home_data');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home_data');
        });
    }
}
