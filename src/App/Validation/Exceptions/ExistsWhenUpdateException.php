<?php

namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class ExistsWhenUpdateException extends ValidationException {

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Has already been taken'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'This does not exist'
        ]
    ];

}
