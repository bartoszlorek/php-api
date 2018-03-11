<?php

namespace App\Models;

/**
 * @property integer            id
 * @property string             file
 * @property string             name
 * @property string             type
 * @property \Carbon\Carbon     created_at
 * @property \Carbon\Carbon     updated_at
 */
class Media extends BaseModel {

    protected $fillable = [
        'file',
        'name',
        'type'
    ];
    
    /*
     *  Relationships
     */
    public function comment() {
        return $this->belongsTo(Comment::class);
    }

}
