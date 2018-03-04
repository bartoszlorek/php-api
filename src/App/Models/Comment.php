<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer                 id
 * @property string                  body
 * @property integer                 page_id
 * @property integer                 user_id
 * @property \Carbon\Carbon          created
 */
class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'body',
        'page_id',
        'user_id',
    ];

    public $timestamps = false;

    /*
     *  Relationships
     */

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
