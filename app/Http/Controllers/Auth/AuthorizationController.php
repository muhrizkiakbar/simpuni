<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Outputs\Admin\UserOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'type_user' => $request->type_user,
            'instansi' => $request->instansi,
            'jabatan' => $request->jabatan,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return $this->render_json(UserOutput::class, "format", $user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|max:15',
            'password' => 'required',
        ]);

        $user = User::
            where('state', 'active')
            ->where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        // Create a personal access token
        $tokenResult = $user->createToken('auth_token', ['access-api'], now()->addMinutes(10080));
        $accessToken = $tokenResult->plainTextToken;

        $user_output = new UserOutput();
        // Return response in desired format
        return response()->json(
            [
                'token_type' => 'Bearer',
                'expires_in' => $tokenResult->accessToken->expires_at, // Default expiration or a custom value
                'access_token' => $accessToken,
                'user' => $user_output->renderJson($user, "format", [ "mode" => "raw_data" ])
            ]
        );
    }


    // Logout user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

}

