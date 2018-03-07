<?php

namespace App\Services\Auth;

use App\Models\User;
use DateTime;
use Firebase\JWT\JWT;
use Illuminate\Database\Capsule\Manager;
use Slim\Collection;
use Slim\Http\Request;
use Hashids\Hashids;

class Auth {

    const SUBJECT_IDENTIFIER = 'email';

    private $db;
    private $appConfig;
    private $hashids;

    public function __construct(Manager $db, Collection $appConfig) {
        $this->db = $db;
        $this->appConfig = $appConfig;
        $this->hashids = new Hashids('', 6);
    }

    /**
     * Generate a new JWT token
     */
    public function generateToken(User $user) {
        $now = new DateTime();
        $future = new DateTime('now +2 hours');

        $payload = [
            'iat' => $now->getTimeStamp(),
            'exp' => $future->getTimeStamp(),
            'jti' => base64_encode(random_bytes(16)),
            'iss' => $this->appConfig['app']['url'], // Issuer
            'sub' => $user->{self::SUBJECT_IDENTIFIER}
        ];

        $secret = $this->appConfig['jwt']['secret'];
        $token = JWT::encode($payload, $secret, 'HS256');
        return $token;
    }

    /**
     * Attempt to find the user based on email and verify password
     */
    public function attempt($email, $password) {
        if (!$user = User::where('email', $email)->first()) {
            return null;
        }
        if (password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    /**
     * Retrieve a user by the JWT token from the request header:
     * Authorization: "Bearer {token}"
     */
    public function requestUser(Request $request) {
        if ($token = $request->getAttribute('token')) {
            return User::where(static::SUBJECT_IDENTIFIER, '=', $token['sub'])->first();
        }
        return null;
    }

    /**
     * Generate a new GUID
     */
    public function generateGuid(int $id) {
        return $this->hashids->encode($id);
    }

}
