<?php

namespace App\Firebase;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Throwable;

/**
 * A class to wrap firebase and handle exceptions from firebase, minimizing the business logic
 * in the controller
 */
class FirebaseUserProvider implements UserProvider
{
    protected $hasher;
    protected $model;
    protected $auth;
    public function __construct() {
       $this->auth = app('firebase.auth');
    }
    public function retrieveById($identifier) {
       $firebaseUser = $this->auth->getUser($identifier);
       return new GenericUser([
        'id' => $firebaseUser->id,
        'email' => $firebaseUser->email,
       ]);
    }
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}
    public function retrieveByCredentials(array $credentials) {}
    public function validateCredentials(Authenticatable $user, array $credentials) {}
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false){}
    
}