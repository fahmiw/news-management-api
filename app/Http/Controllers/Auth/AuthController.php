<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request) {
        try {
            $request->validate([
                    "email" => "required|string|email",
                    "password" => "required|string"
            ]);

            $credentials = request(["email", "password"]);
            
            if(!Auth::attempt($credentials))
                return response()->json([
                "statusCode" => 401,
                "message" => "Unauthorized"
            ], 401);

            $user = $request->user();
            $tokenResult = $user->createToken("Personal Access Token");
            $token = $tokenResult->token;

            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            
            $token->save();

            return response()->json([
                "statusCode" => 200,
                "message" => "Token generated",
                "data" => [
                    "access_token" => $tokenResult->accessToken,
                    "expires_at" => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function register(Request $request) {
        try {
            $request->validate([
                    "name" => "required|string",
                    "email" => "required|string|email|unique:users",
                    "password" => "required|string",
                    "role" => "required"
            ]);

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "role" => $request->role
            ]);

            return response()->json([
                "statusCode" => 201,
                "message" => "Successfully created user!",
                "data" => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function logout(Request $request) {
        try{
            $request->user()->token()->revoke();
            return response()->json([
                "statusCode" => 200,
                "message" => "Successfully logged out"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function user(Request $request) {
        return response()->json($request->user());
    }
}
