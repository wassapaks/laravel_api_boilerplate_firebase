<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function index();
    public function getById($id);
    public function getRole($id);

}
