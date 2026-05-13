<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Content;

class Event extends Model
{
    protected $table = 'event';
        
    protected $fillable = [
        'vendor',
        'start_date',
        'end_date',
        'name_event',
        'location',
        'content_id',
        'file',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

}
