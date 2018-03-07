<?php

namespace App\Helpers;

use League\Fractal\Serializer\ArraySerializer;

/* 
 * $resource = new Collection($data, new Transformer(), 'resourceKey');
 * resourceKey [String] is optional
 */
class CustomArraySerializer extends ArraySerializer {

    public function collection($resourceKey, array $data) {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }

    public function item($resourceKey, array $data) {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }

}