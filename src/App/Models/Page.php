<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
class Page extends Model {

    protected $attributes = [
        'type' => 'page',
        'status' => 'active',
        'state' => '',
        'body' => ''
    ];

    protected $fillable = [
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

    public function scopeWhereInUsers($query, $userId) {
        return $query->whereHas('users', function($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        });
    }

}
