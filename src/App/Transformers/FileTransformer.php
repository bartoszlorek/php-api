<?php

namespace App\Transformers;

use App\Models\File;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract {

    public function transform(File $file) {
        return [
            'id' => (int) $file->id,
            'path' => $file->path,
            'name' => $file->name,
            'type' => $file->type,
            'created' => optional($file->created_at)->toIso8601String(),
            //'updated' => optional($file->updated_at)->toIso8601String()
        ];
    }

}
