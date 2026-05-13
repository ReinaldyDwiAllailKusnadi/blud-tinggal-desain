<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\submission;
use App\Models\ContentFeature;

class Content extends Model
{
    protected $table = 'content';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_weekday',
        'price_weekend',
        'open_time',
        'close_time',
        'location',
        'location_embed',
        'image',
        'instagram',
        'tiktok',
        'whatsapp',
        'capacity',
        'venue_type',
        'is_indoor',
        'is_outdoor',
    ];

    public function event()
    {
        return $this->hasMany(Event::class, 'name');
    }

    public function submission()
    {
        return $this->hasMany(Submission::class, 'name');
    }

    public function features()
    {
        return $this->hasMany(ContentFeature::class, 'location');
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('wisata_all');
            \Illuminate\Support\Facades\Cache::forget('home_data');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('wisata_all');
            \Illuminate\Support\Facades\Cache::forget('home_data');
        });
    }
}
