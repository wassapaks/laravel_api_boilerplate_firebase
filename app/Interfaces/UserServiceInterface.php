<?php
namespace App\Interfaces;

interface UserServiceInterface
{
    public function createUser($data);

    public function update($data, $id);

    public function destroy($id);

}
