<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\ApiResponseClass;
use Exception;
use Illuminate\Validation\ValidationException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\RateLimiter;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    private $firebase;

    public function __construct()
    {
        $this->firebase = Firebase::auth();
    }

    /**
     * Login Function
     * Login function using firebase
     * @param Request $request
     * @return ApiResponseClass
     */
    public function login(Request $request): ApiResponseClass
    {
        // if (RateLimiter::tooManyAttempts('login-attempt:'.$request->ip(), $perMinute = 5)) {
        //     return ApiResponseClass::tooManyRequest($request->ip());
        // }
        // RateLimiter::increment('login-attempt:'.$request->ip());

        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $user = $this->firebase->signInWithEmailAndPassword($request->email, $request->password);
            return ApiResponseClass::ok($user->data());
        } catch (FailedToSignIn $e) {
            return ApiResponseClass::throw($e, 'Username or Password is wrong.', 401);
        } catch (ValidationException $e){
            return ApiResponseClass::throw($e, 'Missing parameters!', 400);
        } catch (Exception $e){
            return ApiResponseClass::throw($e);
        }
    }

    public function verifyToken(Request $request): ApiResponseClass
    {
        $token = $request->bearerToken();
        try {
            $verifiedIdToken = $this->firebase->verifyIdToken($token);
            $user = $request->user;
            return ApiResponseClass::ok($user);
        } catch (FailedToVerifyToken $e) {
            return ApiResponseClass::forbidden($e);
        } catch (Exception $e){
            return ApiResponseClass::throw($e);
        }
    }

    public function roles(Request $request): ApiResponseClass
    {
        $token = $request->bearerToken();
        try {
            $verifiedIdToken = $this->firebase->verifyIdToken($token);
            $user = $request->user;
            return ApiResponseClass::ok($user);
        } catch (FailedToVerifyToken $e) {
            return ApiResponseClass::forbidden($e);
        } catch (Exception $e){
            return ApiResponseClass::throw($e);
        }
    }
}
