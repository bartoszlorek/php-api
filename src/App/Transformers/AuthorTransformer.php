<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract {

    protected $requestUserId;

    public function __construct($requestUserId = null) {
        $this->requestUserId = $requestUserId;
    }

    public function transform(User $user) {
        return [
            'username' => $user->username,
            'role' => $user->role
        ];
    }

}
