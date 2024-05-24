<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        // Validate Register Request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|:max:255',
            'phone' => 'nullable|sometimes|numeric',
            'password' => 'required|max:255',
            'type' => 'required|string',
        ]);

        // Create a New User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ]);
        // Get Token for Authenticated User
        $token = $user->createToken('authToken')->plainTextToken;

        // Create a Response for Registered
        $loginResponse = [
            'user' => UserResource::make($user),
            'token' => $token
        ];

        return response()->json(
            [
                'status' => 201,
                'message' => 'User Registered Successfully',
                'data' => $loginResponse
            ]
        );
    }

    public function login(Request $request)
    {

        // Validate the request
        $request->validate([
            'email' => 'required|string|:max:255',
            'password' => 'required|max:255',
        ]);


        // If User email doesn't exist in the system or credentials doesn't match ( Can be Seperated Checks )
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Credentials'
            ]);
        }

        // Get Token for Authenticated User
        $token = $user->createToken('authToken')->plainTextToken;

        // Create a Response for Registered
        $loginResponse = [
            'user' => UserResource::make($user),
            'token' => $token
        ];

        return response()->json([
            'status' => 200,
            'message' => 'You have successfully logged in.',
            'data' => $loginResponse

        ]);
    }

    public function logout(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully.',
            'user' => $user
        ]);
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = ($query = User::query());

        $user = $user->where($query->qualifyColumn('email'), $request->input('email'))->first();

        //If User doesn't exist then throw an error.
        if (!$user || !$user->email) {
            return response()->json([
                'status' => 404,
                'message' => 'No Record Found or Incorrect Email Address Provided'
            ]);
        }

        //Generate a 4 digit random Token
        $resetPasswordToken = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        //If a User has already requested for forgot password, don't create another record
        //Instead Update the existing token with the new token.
        if (!$userPassResetToken = PasswordResetToken::where('email', $user->email)->first()) {
            //Store Token in DB with Token Expiration Time i.e: 1 hour
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => $resetPasswordToken
            ]);
        } else {
            //Store Token in DB with Token Expiration Time i.e: 1 Hour
            $userPassResetToken->update([
                'email' => $user->email,
                'token' => $resetPasswordToken
            ]);
        }

        //Send Notification to the user about the reset token
        $user->notify(
            new PasswordResetNotification(
                $user,
                $resetPasswordToken
            )
        );

        return new JsonResponse([
            'status' => 200,
            'message' => 'A code has been Sent to your Email Address.'
        ]);
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function reset(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|string|:max:255',
            'token' => 'required|string',
            'password' => 'required|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        // Throw Exception if user is not found
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'No Record Found or Incorrect Email Address Provided.'
            ]);
        }

        $resetRequest = PasswordResetToken::where('email', $user->email)->first();

        if (!$resetRequest || $resetRequest->token != $request->token) {
            return response()->json([
                'status' => 400,
                'message' => 'An error occurred. Please verify the data provided and try again.'
            ]);
        }

        // Update User's Password
        $user->fill([
            'password' => Hash::make($request->password)
        ]);

        $user->save();

        // Delete All Previous Tokens
        $user->tokens()->delete();

        $resetRequest->delete();

        // Get Token for Authenticated User
        $token = $user->createToken('authToken')->plainTextToken;

        // Create a Response
        $loginResponse = [
            'user' => UserResource::make($user),
            'token' => $token
        ];

        return response()->json(
            [
                'status ' => 201,
                'message' => 'Password Reset Successful.',
                'data' => $loginResponse
            ]
        );
    }
}
