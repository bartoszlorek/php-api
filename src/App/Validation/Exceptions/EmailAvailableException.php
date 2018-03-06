<?php

namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class EmailAvailableException extends ValidationException {

    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'e-mail already exists'
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'e-mail does not exist'
        ]
    ];

}
