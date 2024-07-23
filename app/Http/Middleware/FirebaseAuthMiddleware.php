<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use \Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Throwable;

class FirebaseAuthMiddleware
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error_message' => 'Unauthorized'], 401);
        }

        try {
            $verifiedToken = $this->firebaseAuth->verifyIdToken($token);

            $user = UserRepository::getByIdUser($verifiedToken->claims()->get('sub'));

            $request->merge(['user' => $user]);

            //add this
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            
        } catch (Exception $e) {
            return response()->json(['error_message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
