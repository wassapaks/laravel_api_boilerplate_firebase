<?php

namespace App\Services;

use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Interfaces\UserRepositoryInterface;
use App\Services\FirebaseService;
use App\Classes\ApiResponseClass;
use App\Http\Resources\UserResource;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;

    private FirebaseService $firebaseService;

    /**
     * Create a new class instance.
     */
    public function __construct(UserRepositoryInterface $userRepository, FirebaseService $firebaseService)
    {
        $this->userRepository = $userRepository;
        $this->firebaseService = $firebaseService;
    }

    public function createUser($request) {
        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'c_password' => $request->cpassword,
            'role_id' => 1,
        ];

        DB::beginTransaction();

        try{

            $user = User::create($details);
            $details['id'] = $user->id;

            //When a user is created, we create a firebase account as well
            if(!$this->firebaseService->createFirebaseAuth($details)){
                throw new \Exception("Cannot create Firebase Auth"); 
            }

            $user->assignRole($request->roles);
            $user->givePermissionTo('create-users', 'delete-users', 'edit-users', 'view-users');

            //The account is not yet fully usable they have to verify the link in their given email
            $this->firebaseService->sendVerificationLink($user->email, env('FIREBASE_CONTINUE_URL'));

            DB::commit();
            return ApiResponseClass::created(new UserResource($user));

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function update($data, $id) {

        $details = [
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
            'c_password' => $data->cpassword,
        ];
        
        DB::beginTransaction();
        try{
            $user = User::whereId($id)->update($data);
            DB::commit();
            return $user ?
            ApiResponseClass::updated(new UserResource($data)) :
            ApiResponseClass::okButResourceNotFound();
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function destroy($id){
        return User::destroy($id) ? 
        ApiResponseClass::deleted():
        ApiResponseClass::okButResourceNotFound();
    }

    public function getUserRolePermissions($id){
        try{
            $role = $this->userRepository->getRole($id);
            $permission = $this->userRepository->getPermission($id);
            $data = [
                'roles' => $role ?: [],
                'permissions' => $permission ?:[]
            ];
            return ApiResponseClass::ok($data);
        }catch (\Exception $ex) {
            return ApiResponseClass::okButResourceNotFound();
        }
    }

}
