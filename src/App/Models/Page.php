<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer            id
 * @property string             title
 * @property string             slug
 * @property string             body
 * @property \Carbon\Carbon     created_at
 * @property \Carbon\Carbon     updated_at
 */
class Page extends Model {
    protected $fillable = [
        'title',
        'slug',
        'body',
    ];

    public function setSlugAttribute($value) {
        $index = 0;
        $slug = $value;
        while (self::newQuery()
            ->where('slug', $slug)
            ->where('id', '!=', $this->id)
            ->exists()) {
            $slug = $value . '-' . ++$index;
        }
        
        return $this->attributes['slug'] = $slug;
    }

    /*
     *  Relationships
     */

    public function users() {
        return $this->belongsToMany(User::class, 'user_pages');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function addUser($user_id) {
        $this->users()->syncWithoutDetaching([$page_id]);
        return $this;
    }

    public function removeUser($user_id) {
        $this->users()->detach($user_id);
        return $this;
    }

    public function isUserPage($user_id = null) {
        if (is_null($user_id)) {
            return false;
        }
        if ($user_id instanceof self) {
            $user_id = $user_id->id;
        }
        return $this->newBaseQueryBuilder()
            ->from('user_pages')
            ->where('user_id', $user_id)
            ->where('page_id', $this->id)
            ->exists();
    }
}
