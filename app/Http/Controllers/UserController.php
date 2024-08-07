<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    private UserServiceInterface $userService;

    private FirebaseService $firebaseService;

    public function __construct(UserRepositoryInterface $userRepository, FirebaseService $firebaseService, UserServiceInterface $userService){
        $this->userRepository = $userRepository;
        $this->firebaseService = $firebaseService;
        $this->userService = $userService;
    }

    public function store(StoreUserRequest $request): ApiResponseClass
    {
        return $this->userService->createUser($request);
    }

    public function update(StoreUserRequest $request, $id): ApiResponseClass
    {
        return $this->userService->update($request, $id);
    }

    public function destroy($id): ApiResponseClass
    {
        return $this->userService->destroy($id);
    }

    public function show($id) : ApiResponseClass {
        try{
            $book = $this->userRepository->getById($id);
            return ApiResponseClass::ok(new UserResource($book));
        }catch (\Exception $ex) {
            return ApiResponseClass::okButResourceNotFound();
        }
    }
    public function rolesPermissions(Request $request) : ApiResponseClass {
        return $this->userService->getUserRolePermissions($request->user->id);
    }
}
