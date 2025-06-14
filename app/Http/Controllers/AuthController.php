<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $request->validate([
            'name'=>'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password),
        ]);

        return response()->json(['token'=>$user->createToken('token')->plainTextToken],200);
    }

    public function login(Request $request){
        $request->validate(['email'=>'required','password'=> 'required']);

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages(['email'=> 'invalid credentials']);
        }

        return response()->json(['token'=>$user->createToken('token')->plainTextToken],200);
    }
}
