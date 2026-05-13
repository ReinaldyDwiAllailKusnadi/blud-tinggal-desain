<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Content;

use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use SoftDeletes;

    protected $table = 'submission';

    protected $fillable = [
        'user_id',
        'namePIC',
        'no_hp',
        'address',
        'vendor',
        'location',
        'content_id',
        'apply_date',
        'start_date',
        'end_date',
        'name_event',
        'file',
        'ktp',
        'appl_letter',
        'actv_letter',
        'status',
        'notes',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

}
