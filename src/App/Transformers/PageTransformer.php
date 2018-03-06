<?php

namespace App\Transformers;

use App\Models\Page;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract {

    // protected $excludes;

    // public function __construct($excludes) {
    //     $this->excludes = $excludes;
    // }

    public function transform(Page $page) {
        return [
            "id" => $page->id,
            "slug" => $page->slug,
            "title" => $page->title,
            "body" => $page->body,
            'created' => $page->created_at->toIso8601String(),
            'updated' => $page->updated_at->toIso8601String()
        ];
    }
    
}
