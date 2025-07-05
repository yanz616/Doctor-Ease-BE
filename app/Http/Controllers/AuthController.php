<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $request->validate([
            'name'=>'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|confirmed|min:6',
            'avatar'=> 'nullable|url',
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'avatar'=> $request->avatar,
            'password'=> bcrypt($request->password),
            'is_admin'=> false,
        ]);

        return response()->json([
            'token'=> $user->createToken('token')->plainTextToken,
            'user' => $user,
            // 'is_admin' => $user->is_admin,
        ],200);
    }

    public function login(Request $request){
        $request->validate(['email'=>'required','password'=> 'required']);

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages(['email'=> 'invalid credentials']);
        }

        return response()->json([
            'token'=>$user->createToken('token')->plainTextToken,
            // 'is_admin'=> $user->is_admin,
            'user' => $user,
        ],200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil. Token dihapus.'
        ]);
    }

    // Update profile user yang sedang login
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|url',
            'password' => 'nullable|confirmed|min:6', // ⬅ password & confirmation
        ]);

        // Jika password dikirim, enkripsi dulu
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']); // Jangan ubah password jika kosong
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile berhasil diperbarui.',
            'data' => $user
        ]);
    }


    public function me(Request $request)
    {
        return response()->json($request->user());
    }

}
