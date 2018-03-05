<?php

namespace App\Transformers;

use App\Models\Page;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract {

    public function transform(Page $page) {
        return [
            "id" => $page->id,
            "slug" => $page->slug,
            "title" => $page->title,
            "body" => $page->body,
            'created' => $page->created_at->toIso8601String(),
            'updated' => isset($user->update_at) ? $page->update_at->toIso8601String() : $page->update_at
        ];
    }
    
}
