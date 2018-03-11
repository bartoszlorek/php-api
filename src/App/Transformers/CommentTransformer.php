<?php

namespace App\Transformers;

use App\Models\Comment;
use App\Transformers\UserAttachedTransformer;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract {

    protected $defaultIncludes = ['user'];

    public function transform(Comment $comment) {
        return [
            'id' => (int) $comment->id,
            'body' => !empty($comment->body) ? $comment->body : '',
            'created' => $comment->created_at->toIso8601String(),
            'updated' => $comment->updated_at->toIso8601String()
        ];
    }

    public function includeUser(Comment $comment) {
        return $this->item($comment->user, new UserAttachedTransformer);
    }

}
