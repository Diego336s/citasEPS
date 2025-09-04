<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function registrar(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "name" => "required|string",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $validator->errors()
            ], 422);
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([
            "seccess" => true,
            "message" => "$request->name sea registrado exitosamente",
            "token_access" => $token,
            "token_type" => "Bearer"
        ], 201);
    }
    
    
    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            "email" => "required|email",
            "password" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "seccess" => false,
                "error" => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only("email", "password"))) {
            return response()->json([
                "success" => false,
                "message" => "Credenciales incorrectas"
            ], 401);
        }

        $user = User::where("email", $request->email)->firstOrfail();
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "Token" => $token,
            "token_type" => "Bearer"
        ]);
    }



    public function me(Request $request)
    {
        return response()->json([
            "success" => true,
            "user" => $request->user()
        ]); 
    }
}
