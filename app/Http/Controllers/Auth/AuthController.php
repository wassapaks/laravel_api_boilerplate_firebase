<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\ApiResponseClass;
use Illuminate\Http\JsonResponse;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\RateLimiter;

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
     * @return JsonREsponse
     */
    public function login(Request $request): JsonResponse
    {
        // if (RateLimiter::tooManyAttempts('login-attempt:'.$request->ip(), $perMinute = 5)) {
        //     return ApiResponseClass::sendResponse('', 'Too many attempts from: '.$request->ip(), 401);
        // }

        // RateLimiter::increment('login-attempt:'.$request->ip());

        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $user = $this->firebase->signInWithEmailAndPassword($request->email, $request->password);
            
            return ApiResponseClass::sendResponse($user->data(), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse('', 'Username or Password is wrong.', 401);
        }
    }

    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        try {
            $verifiedIdToken = $this->firebase->verifyIdToken($token);
            $user = $this->firebase->getUser($verifiedIdToken->claims()->get('sub'));
            return ApiResponseClass::sendResponse($user, '', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse($e->getMessage(), '', 401);
        }
    }
}
