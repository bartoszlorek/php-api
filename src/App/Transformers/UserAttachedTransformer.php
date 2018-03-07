<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserAttachedTransformer extends TransformerAbstract {

    public function transform(User $user) {
        return [
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->role
        ];
    }

}
