<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentFeature  extends Model
{
    protected $table = 'content_features';

    protected $fillable = [
        'location', // foreign key ke Content
        'type',
        'bagian', 
        'luas', 
        'price',
        'facility_name', 
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function content()
    {
        // foreign key di anak: "location"
        return $this->belongsTo(Content::class, 'location');
    }

    
    // public function scopePrices($q)
    // { 
    //     return $q->where('type', 'price'); 
    // }

    // public function scopeFacilities($q)
    // { 
    //     return $q->where('type', 'facility'); 
    // }
}
