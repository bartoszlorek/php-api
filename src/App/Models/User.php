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

    protected $attributes = [
        'role' => 'user'
    ];

    protected $fillable = [
        'email',
        'username',
        'password'
    ];

    protected $hidden = [
        'password',
    ];
    
    /*
     *  Relationships
     */
    public function pages() {
        return $this->belongsToMany(Page::class, 'pages_users');
    }

}
