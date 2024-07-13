<?php

namespace App\Policies;

use App\Models\Books;

class BookPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(Books $user)
    {
        return $user->hasRole('edit articles');
    }
 
    public function update(Books $user)
    {
        return $user->hasRole('publish articles');
    }
}
