<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class BaseModel extends Model {

    public function set(array $data, array $fields) {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->{$field} = $data[$field];
            }
        }
    }

    /**
     * Insert multiple records to the table
     * @return array
     */
    public static function createMany(array $data = []) {
        if (empty($data)) {
            return collect();
        }
        $now = Carbon::now()->toDateTimeString();
        foreach ($data as &$row) {
            $row[self::CREATED_AT] = $now;
            $row[self::UPDATED_AT] = $now;
        }
        if (!self::insert($data)) {
            return collect();
        }
        
        // an attempt to predict new ids
        $lastId = self::orderBy('id', 'desc')->first()->id;
        $lastId -= count($data);

        foreach ($data as &$row) {
            $row['id'] = ++$lastId;
        }
        return self::hydrate($data);
    }

}
