<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;

class UserController extends BaseController
{
    //This login functionality is for admins.

    public function login(Request $request){
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('login');
            return $this->respondWithToken($token->plainTextToken);
        }
    }
    //register an admin
    public function register(Request $request){
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'phone_number'=>['required', 'string', 'max:20'],
            'email'=>['required','email'],
            'password'=>['required', 'string', 'max:100'],
            'role'=>['nullable', 'string', 'max:100'],
        ]);

        //generate token
       

        //create record
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'=> $data['last_name'],
            'email'=>$data['email'],
            'phone_number'=>$data['phone_number'],
            'username'=>$data['username'],
            'password'=>bcrypt($data['password']),
            'role'=>$data['role']
        ]);

        $token = $user->createToken('register');
        $info =[
            'access_token'=> $token->plainTextToken,
            'user' => $user
        ];
        return $this->sendResponse($info, 'Registration successful.');
        
    }

    protected function respondWithToken($token)
    {
        $user =  auth()->user();
        $info = [
            'access_token' => $token,
            'user' => $user,
        ];
        return $this->sendResponse($info, 'Login successful');
    }

}
