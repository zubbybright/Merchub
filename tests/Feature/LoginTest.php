<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\AdminSeeder;
use App\Models\Admin;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    const AUTH_URL = '/api/auth/login';
    protected $admin;

    protected function setUp(): void{
        parent::setUp();
        $this->seed(AdminSeeder::class);
        $this->admin = Admin::first();   
    }

    public function test_an_admin_can_login()
    {
        $response = $this->postjson(self::AUTH_URL,[
            "username" => $this->admin->username,
            "password" => "password",
        ]);
        
        $response->assertStatus(200);
    }

    public function test_an_admin_cannot_login_with_wrong_credetials(){
        $response = $this->postjson(self::AUTH_URL,[
            "username" => "username",
            "password" => "904mjdjd",
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'message' => "Invalid credentials",
        ]);
    }
}
