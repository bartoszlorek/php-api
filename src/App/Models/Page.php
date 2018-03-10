<?php

namespace App\Models;

/**
 * @property integer            id
 * @property string             guid
 * @property string             type
 * @property string             status
 * @property string             title
 * @property string             state
 * @property string             body
 * @property \Carbon\Carbon     created_at
 * @property \Carbon\Carbon     updated_at
 */
class Page extends BaseModel {

    const TYPE_PAGE = 'page';

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';
    const STATUS_PAYMENT = 'payment';

    protected $attributes = [
        'type' => self::TYPE_PAGE,
        'status' => self::STATUS_ACTIVE,
        'state' => '',
        'body' => ''
    ];

    protected $fillable = [
        'type',
        'title',
        'body'
    ];

    /*
     *  Relationships
     */
    public function users() {
        return $this->belongsToMany(User::class, 'pages_users');
    }

    public function attachUser(int $userId) {
        $this->users()->syncWithoutDetaching($userId);
        return $this;
    }

    public function detachUser(int $userId) {
        $this->users()->detach($userId);
        return $this;
    }

    public function containsUser($userId) {
        return $this->users()
            ->where('user_id', $userId)
            ->exists();
    }

    public function scopeWhereInUsers($query, $userId) {
        return $query->whereHas('users', function($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        });
    }

}
