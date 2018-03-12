<?php

namespace App\Models;

/**
 * @property integer            id
 * @property string             path
 * @property string             name
 * @property string             type
 * @property integer            comment_id
 * @property \Carbon\Carbon     created_at
 * @property \Carbon\Carbon     updated_at
 */
class File extends BaseModel {

    protected $fillable = [
        'path',
        'name',
        'type',
        'comment_id'
    ];

    /*
     *  Relationships
     */
    public function comment() {
        return $this->belongsTo(Comment::class);
    }

}