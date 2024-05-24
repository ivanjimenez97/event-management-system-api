<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|:max:255', // Use unique validation rule
            'password' => 'required|max:255',
            'type' => 'required|string|max:100',
        ]);

        // Check if user with the same email exists
        $userExists = User::where('email', $request->email)->first();

        if ($userExists) {
            return response()->json([
                'user' => $userExists,
                'status' => 406,
                'message' => 'Another account with The same Email already exists. Please make sure You are using a new and unique Email.'
            ]);
        }

        // Create a new user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type = $request->type;

        try {
            $user->save();
            return response()->json([
                'status' => 201, // Created
                'message' => 'User generated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'User not generated. Please verify the data and send the request again.'
            ]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = User::where('email', $credentials['email'])->firstOrFail();
            $token = $user->createToken('userToken')->plainTextToken;
            return response()->json([
                'status' => 200,
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json([
            'errors' => [
                'status' => 401,
                'message' => 'Invalid Credentials.',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'status' => '200',
            'message' => 'Session closed successfully.'
        ]);
    }
}
