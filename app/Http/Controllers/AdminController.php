<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;

class AdminController extends BaseController
{
    //This login functionality is for admins.

    public function login(Request $request){
        $credentials = $request->only('username', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->sendError('Invalid credentials', 'Invalid credentials');
        }
        $token = $request->user()->createToken('login');
        return $this->respondWithToken($token->plainTextToken);
    }
    //register an admin
    public function register(Request $request){
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string','unique:admins', 'max:255'],
            'phone_number'=>['required', 'string','unique:admins', 'max:20'],
            'email'=>['required','email', 'unique:admins'],
            'password'=>['required', 'string', 'max:100', 'confirmed'],
            'role'=>['nullable', 'string', 'max:100'],
        ]);

        //create record
        $admin = Admin::create([
            'first_name' => $data['first_name'],
            'last_name'=> $data['last_name'],
            'email'=>$data['email'],
            'phone_number'=>$data['phone_number'],
            'username'=>$data['username'],
            'password'=>bcrypt($data['password']),
            'role'=>$data['role']
        ]);

        //generate token 
        $token = $admin->createToken('register');
        $info =[
            'access_token'=> $token->plainTextToken,
            'admin' => $admin
        ];
        return $this->sendResponse($info, 'Registration successful.');
        
    }

    protected function respondWithToken($token)
    {
        $admin =  auth()->user();
        $info = [
            'access_token' => $token,
            'admin' => $admin,
        ];
        return $this->sendResponse($info, 'Login successful');
    }

}
