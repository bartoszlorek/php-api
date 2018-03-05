<?php

namespace App\Transformers;

use App\Models\Page;
use App\Models\Comment;
use App\Models\User;
use League\Fractal\ItemResource;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract {

    protected $defaultIncludes = [
        'author'
    ];

    protected $requestUserId;

    public function __construct($requestUserId = null) {
        $this->requestUserId = $requestUserId;
    }

    public function transform(Comment $comment) {
        return [
            'id' => $comment->id,
            'body' => $comment->body,
            'page_id' => $comment->page_id,
            'created' => $comment->created_at->toIso8601String(),
            'updated' => isset($user->update_at) ? $comment->update_at->toIso8601String() : $comment->update_at
        ];
    }

    public function includeAuthor(Comment $comment) {
        $author = $comment->user_id;
        return $this->item($author, new AuthorTransformer($this->requestUserId));
    }

}
