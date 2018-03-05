<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer            id
 * @property string             email
 * @property string             username
 * @property string             password
 * @property string             role
 * @property string             token
 * @property \Carbon\Carbon     created_at
 * @property \Carbon\Carbon     updated_at
 */
class User extends Model {

    protected $fillable = [
        'email',
        'username',
        'password',
        'role',
        'token',
    ];

    protected $hidden = [
        'password',
    ];
    
    /*
     *  Relationships
     */

    public function pages() {
        return $this->belongsToMany(Page::class, 'user_pages');
    }

    public function hasAccessToPage($page_id = null) {
        if (is_null($page_id)) {
            return false;
        }
        if ($page_id instanceof self) {
            $page_id = $page_id->id;
        }
        return $this->newBaseQueryBuilder()
            ->from('user_pages')
            ->where('user_id', $this->id)
            ->where('page_id', $page_id)
            ->exists();
    }
    
}
