<?php

namespace App\Transformers;

use App\Models\Page;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    public function transform(Page $page)
    {
        return [
            "id" => $page->id,
            "slug" => $page->slug,
            "title" => $page->title,
            "body" => $page->body,
            'created' => $page->created->toIso8601String(),
            'updated' => isset($user->updated) ? $page->updated->toIso8601String() : $page->updated,
        ];
    }
}
