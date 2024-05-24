<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(int $id)
    {
        //$authUser = auth('api')->user();
        $user = User::where('id', $id)->first();

        return response()->json([
            'user' => $user,
            //'authUser' =>  $authUser
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|sometimes|numeric',
            'password' => 'required|max:255',
            'type' => 'required|string',
        ]);

        $user = User::where('id', $request->id)->firstOrFail();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->type = $request->type;


        if ($user->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Profile updated successfully.',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'An Error Occured. Please verify the data provided.'
            ]);
        }
    }
}
