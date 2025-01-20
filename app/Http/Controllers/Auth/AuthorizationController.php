<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Outputs\Admin\UserOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller
{
    // Register a new user
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

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me()
    {
        return $this->render_json(UserOutput::class, "format", Auth::user());
    }

    public function change_profile(Request $request)
    {
        $request->validate([
            'instansi' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed',
            'avatar' => 'nullable|mimetypes:image/jpeg,image/png,image/jpg|max:512',
        ]);

        $user =  User::find(Auth::user()->id);

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filePath = $file->store('avatars', 'public'); // Save to 'storage/app/public/avatars'

            $user->avatar = $filePath;
            $user->save();
        }

        $user->instansi = $request->instansi;
        $user->posisi = $request->posisi;
        $user->name = $request->name;
        $user->save();

        return $this->render_json(UserOutput::class, "format", $user);
    }
}

