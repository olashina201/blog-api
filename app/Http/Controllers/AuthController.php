<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function register(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'fullname' => ['required', 'min:6', 'max:100'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'max:18'],
            'confirm_password' => ['required', 'same:password']
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'validation fails',
                'error' => $validate->errors()
            ], 422);
        }

        $user = User::create([
            'fullname' => $req->fullname,
            'email' => $req->email,
            'password' => Hash::make($req->password)
        ]);

        return response()->json([
            'message' => 'user registered successfully',
            'data' => $user
        ], 200);
    }

    //Login
    public function login(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'validation fails',
                'error' => $validate->errors()
            ], 422);
        }

        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password,  $user->password)) {
            return response()->json([
                'message' => 'email or password not correct',
            ], 400);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successfully',
            'token' => $token,
            'data' => $user
        ], 200);
    }
}
