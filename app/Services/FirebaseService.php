<?php

namespace App\Services;

use Google\Protobuf\BoolValue;
use Kreait\Firebase\Contract\Auth;
use Illuminate\Support\Facades\Log;

/**
 * A class to wrap firebase and handle exceptions from firebase, minimizing the business logic
 * in the controller
 */
class FirebaseService
{
    private $auth;
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function createFirebaseAuth(array $details)
    {
        try {
            $user = $this->auth->createUser([
                'uid' => $details['id'],
                'email' => $details['email'],
                'password' => $details['password'],
                'displayName' => $details['name']
            ]);
            return $user;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    public function sendVerificationLink($email, $continueURL): bool
    {
        try {
            $actionCodeSettings = [
                'continueUrl' => $continueURL
            ];
            $this->auth->sendEmailVerificationLink($email, $actionCodeSettings);
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    public function deleteUserAuth($uid)
    {
        try {
            $this->auth->deleteUser($uid);
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $user = $this->auth->getUserByEmail($email);
            return $user;
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }
}
