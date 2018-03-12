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

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    /*
     *  Methods
     */
    public function attachUser(int $userId) {
        $this->users()->syncWithoutDetaching($userId);
        return $this;
    }

    public function detachUser(int $userId) {
        $this->users()->detach($userId);
        return $this;
    }

    public function comment(int $commentId) {
        return $this->comments()->where('id', $commentId)->first();
    }

    /*
     *  Scopes
     */
    public function scopeWhereInUsers($query, int $userId) {
        return $query->whereHas('users', function($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        });
    }

    public function scopeWhereInComments($query, int $commentId) {
        return $query->whereHas('comments', function($subQuery) use ($userId) {
            $subQuery->where('id', $commentId);
        });
    }

}
