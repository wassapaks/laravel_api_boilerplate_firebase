<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\FirebaseService;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Tests\TestCase;

class FirebaseServiceTest extends TestCase
{
    private $data = [];
    private $firebase;
    /**
     * Firebase Test Create User Auth Fail return Bool False
     */
    public function test_create_user_when_record_exists_then_throw_error(): void
    {
        $faker = \Faker\Factory::Create();
        $this->firebase = new FirebaseService(Firebase::auth());
        $this->data = [
            "email"=> $faker->email,
            "password" => $faker->password,
            "name" => $faker->name
        ];
        $user = $this->firebase->createFirebaseAuth($this->data);  
        //Check if duplicate will return exception
        $res = $this->firebase->createFirebaseAuth($this->data);  
        $this->assertFalse($res);
        $del = $this->firebase->deleteUserAuth($user->uid);
    }

    /**
     * Firebase Test Create User Auth Success
     */
    public function test_create_user_when_valid_data_then_result_user_details(){
        $faker = \Faker\Factory::Create();
        $this->firebase = new FirebaseService(Firebase::auth());
        $this->data = [
            "email"=> $faker->email,
            "password" => $faker->password,
            "name" => $faker->name
        ];
        $user = $this->firebase->createFirebaseAuth($this->data);  
        $this->assertNotEmpty($user->uid);
        $this->firebase->deleteUserAuth($user->uid);
    }

    /**
     * Firebase Test Create User Auth Success
     */
    public function test_delete_user_when_invalid_id_then_return_false(){
        $this->firebase = new FirebaseService(Firebase::auth());

        //force invalid uid
        $result = $this->firebase->deleteUserAuth('aaaa');
        $this->assertFalse($result);
    }

    /**
     * Firebase Test Create User Auth Success
     */
    public function test_delete_user_when_valid_id_then_return_true(){
        $faker = \Faker\Factory::Create();
        $this->firebase = new FirebaseService(Firebase::auth());
        $this->data = [
            "email"=> $faker->email,
            "password" => $faker->password,
            "name" => $faker->name
        ];
        $user = $this->firebase->createFirebaseAuth($this->data);  
        $result = $this->firebase->deleteUserAuth($user->uid);
        $this->assertTrue($result);
    }
}
