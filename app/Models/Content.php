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

    
}
