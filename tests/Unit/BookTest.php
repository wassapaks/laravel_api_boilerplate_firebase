<?php

namespace Tests\Unit;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

class BookTest extends TestCase
{
    /**
     * Testing Validations for Book Resources Store
     *
     * @param array $data
     * @return void
     */

    #[DataProvider('invalidDataProvider')]
    #[TestDox('test validation empty fields on store')]
    public function test_it_should_fail_validation_if_empty_author_name_date_on_store(array $data)
    {
        $request = new StoreBookRequest();
        
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }

     /**
     * Testing Validations for Book Resources On Update
     *
     * @param array $data
     * @return void
     */

    #[DataProvider('invalidDataProvider')]
    #[TestDox('test validation empty fields on update')]
    public function test_it_should_fail_validation_if_empty_author_name_date_on_update(array $data)
    {
        $request = new UpdateBookRequest();
        
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->fails());
    }

    public static function invalidDataProvider(){
        $faker = \Faker\Factory::Create();
        return [
            [[
                'name' => $faker->name,
                'author' => '',
                'publish_date' => $faker->date,
            ]],
            [[
                'name' => '',
                'author' => $faker->name,
                'publish_date' => $faker->date,
            ]],
            [[
                'name' => $faker->name,
                'author' => $faker->name,
                'publish_date' => '',
            ]]
        ];
    }

    

}
