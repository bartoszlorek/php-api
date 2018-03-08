<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    public function set(array $data, array $fields) {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->{$field} = $data[$field];
            }
        }
    }

}
