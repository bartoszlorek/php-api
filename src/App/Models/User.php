<?php

namespace App\Models;

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
class User extends BaseModel {

    const ROLE_USER = 'user';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';

    protected $attributes = [
        'role' => self::ROLE_USER
    ];

    protected $fillable = [
        'email',
        'username',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
    
    /*
     *  Relationships
     */
    public function pages() {
        return $this->belongsToMany(Page::class, 'pages_users');
    }

    /*
     *  Methods
     */
    public function isCommonUser() {
        return $this->role === self::ROLE_USER;
    }

    public function isModerator() {
        return $this->role === self::ROLE_MODERATOR;
    }

    public function isAdmin() {
        return $this->role === self::ROLE_ADMIN;
    }

}
