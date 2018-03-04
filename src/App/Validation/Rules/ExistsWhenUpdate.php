<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ExistsWhenUpdate extends AbstractRule
{
    protected $id;
    private $columns;
    private $table;

    public function __construct($table, $columns, $id)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->id = $id;
    }

    public function validate($input)
    {
        return !$this->table->where($this->columns, $input)
            ->where('id', '!=', $this->id)->exists();
    }
}
