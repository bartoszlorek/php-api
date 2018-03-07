<?php

namespace App\Transformers;

use App\Models\Page;
use League\Fractal\TransformerAbstract;

class PageListTransformer extends TransformerAbstract {

    public function transform(Page $page) {
        return [
            'id' => (int) $page->id,
            'guid' => $page->guid,
            'type' => $page->type,
            'status' => $page->status,
            'title' => $page->title,
            'state' => !empty($page->state) ? $page->state : '',
            'created' => $page->created_at->toIso8601String(),
            'updated' => $page->updated_at->toIso8601String()
        ];
    }
    
}
