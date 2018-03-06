<?php

namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class ExistsInTableException extends ValidationException {

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'has already been taken'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'this does not exist'
        ]
    ];

}
