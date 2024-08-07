<?php

namespace App\Repositories;
use App\Models\User;    
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use App\Models\Role;
use Spatie\Permission\Contracts\Permission;

class UserRepository implements UserRepositoryInterface
{
    public function index(){
        return Cache::remember('users', $minutes='60', function()
        {
            return User::all();
        });
    }
    
    public function getById($id){
        return Cache::remember("users.{$id}", $minutes='60', function() use($id)
        {
            return User::findOrFail($id);
        });
    }

    public function store($data){}

    public static function getByIdUser($id){
        return User::select('id', 'name', 'role_id')->where('id', $id)->first();
    }

    public function getRole($id){
        return User::find($id)->getRoleNames();
    }

    public function getPermission($id){
        return User::select('id', 'name', 'role_id')->where('id', $id)->first()->getPermissionNames();
    }
}
