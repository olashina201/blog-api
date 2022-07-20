<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;

class Auth extends Controller
{
    //
    public function register(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'fullname' => 'required|min:6|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:18',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'validation fails',
                'error' => $validator->error()
            ], 422);
        }
        $user = User::create([
            'fullname' => $req->fulname,
            'email' => $req->email,
            'password' => $req->Hash::make($req->password)
        ]);

        return response()->json([
            'message' => 'user registered successfully',
            'data' => $user
        ], 200);
    }
}
