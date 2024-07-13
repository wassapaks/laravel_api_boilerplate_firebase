<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Services\FirebaseService;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepositoryInterface;
    private FirebaseService $firebaseService;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, FirebaseService $firebaseService){
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->firebaseService = $firebaseService;
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'c_password' => $request->cpassword,
            'role_id' => $request->role_id,
        ];

        DB::beginTransaction();
        try{
            $user = $this->userRepositoryInterface->store($details);
            $details['id'] = $user->id;
            //When a user is created, we create a firebase account as well
            if(!$this->firebaseService->createFirebaseAuth($details)){
                throw new \Exception("Cannot create Firebase Auth"); 
            }
            $user->givePermissionTo('edit articles', 'delete articles');
            //The account is not yet fully usable they have to verify the link in their given email
            $this->firebaseService->sendVerificationLink($user->email, env('FIREBASE_CONTINUE_URL'));

            DB::commit();
            return ApiResponseClass::sendResponse(new UserResource($user),'User Create Success!',201);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function update(StoreUserRequest $request, $id): JsonResponse{
        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'c_password' => $request->cpassword,
        ];
        
        DB::beginTransaction();
        try{
            $user = $this->userRepositoryInterface->update($details, $id);
            
            DB::commit();
            return ApiResponseClass::sendResponse(new UserResource($user),'Product Create Success!',201);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function destroy($id): JsonResponse{
        $this->userRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse('User Delete Success', '' , 204);
    }

    public function show($id) {
        $user = $this->userRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new UserResource($user),'',200);
    }
}
