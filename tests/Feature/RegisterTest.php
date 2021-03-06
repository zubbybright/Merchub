<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use Database\Seeders\AdminSeeder;




class RegisterTest extends TestCase
{   
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    const AUTH_URL = '/api/auth/register';
    const INVALID_MESSAGE = 'The given data was invalid.';
    protected $admin;

    public function test_an_admin_can_be_registered_()
    {
        $response = $this->postjson(self::AUTH_URL,[
            "phone_number"=>"22219499944",
            "email"=> "admin1@merchub.com",
            "password"=> "password",
            "username"=>"username",
            "password_confirmation"=> "password",
            "first_name"=> "Super",
            "last_name"=> "Admin",
            "role"=>"Administration"
        ]);
        $response->assertStatus(200);
    }

    public function test_admin_username_phone_email_must_be_unique(){
        $this->seed(AdminSeeder::class);
        $this->admin = Admin::first();

        $response = $this->postjson(self::AUTH_URL,[
            "phone_number"=>$this->admin->phone_number,
            "email"=> $this->admin->email,
            "password"=> "password",
            "username"=>$this->admin->username,
            "password_confirmation"=> "password",
            "first_name"=> "Super",
            "last_name"=> "Admin",
            "role"=>"Administration"
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'username' => ["The username has already been taken."],
            'email' => ["The email has already been taken."],
            'phone_number' => ["The phone number has already been taken."],
        ]);
        
    }
    public function test_an_admin_cannot_be_registered_with_empty_fields()
    {   
        
        $response = $this->postjson(self::AUTH_URL,[
            "phone_number"=>" ",
            "password"=> " ",
            "email"=> " ",
            "username"=> " ",
            "password_confirmation"=> " ",
            "first_name"=> " ",
            "last_name"=> " ",
            "role"=> " "
            
        ]);
    
        $response->assertStatus(422);
        $response->assertJson([
            "message"=> self::INVALID_MESSAGE,
        ]); 
        $response->assertJsonFragment([
            'first_name' => ["The first name field is required."],
            'last_name' => ["The last name field is required."],
            'username' => ["The username field is required."],
            'phone_number' => ["The phone number field is required."],
            'email' => ["The email field is required."],
            'password' =>["The password field is required."]
        ]);
        
    }

    public function test_email_field_must_accept_a_valid_email_format()
    {   
        
        $response = $this->postjson(self::AUTH_URL,[
            'first_name' => 'John',
            'last_name'=>'Doe',
            'phone_number' => '12345678909',
            'email' => 'merchubadmin.admin',
            'password' =>'password' ,
            'password_confirmation' => 'password',
            'username'=>"username",
            'role'=>"role"
            
        ]);
    
        $response->assertStatus(422);
        $response->assertJson([
            "message"=> "The given data was invalid.",
        ]); 
        $response->assertJsonFragment([
            'email' => ['The email must be a valid email address.']
        ]);
    }

    public function test_firstname_and_lastname_cannot_be_numbers()
    {   
        
        $response = $this->postjson(self::AUTH_URL,[
            'first_name' => 102000300,
            'last_name'=>1009399494,
            'username' => 'user1',
            'phone' => '88838383844',
            'email' => 'email@user.com',
            'password' =>'password' ,
            'password_confirmation' => 'password',
            'role'=> 'role'
           
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'first_name' => [ "The first name must be a string."],
                'last_name' =>  ["The last name must be a string."]
            ]
        ]);
    }

    public function test_text_inputs_cannot_be_numbers()
    {   
        
        $response = $this->postjson(self::AUTH_URL,[
            'first_name' => 102000300,
            'last_name'=>1009399494,
            'username' => 9489389,
            'phone' => 88838383844,
            'role' => 2345656,
            'email' => 'email@user.com',
            'password' =>'password' ,
            'password_confirmation' => 'password'
           
        ]);
        $response->assertStatus(422);
       
    }

}
