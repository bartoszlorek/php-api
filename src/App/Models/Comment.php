<?php

namespace App\Models;

/**
 * @property integer            id
 * @property string             body
 * @property integer            user_id
 * @property integer            page_id
 * @property \Carbon\Carbon     created_at
 * @property \Carbon\Carbon     updated_at
 */
class Comment extends BaseModel {

    protected $fillable = [
        'body',
        'user_id',
        'page_id'
    ];

    /*
     *  Relationships
     */

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function page() {
        return $this->belongsTo(Page::class);
    }

}
