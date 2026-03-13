<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Assuming you have a User model
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crm' => ['required', 'string', 'min:4', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $crm = strtoupper(trim($request->crm));
        if (Auth::attempt(['crm' => $crm, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->remember_token ?? Str::random(60);
            $user->remember_token = $token;
            $user->save();

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
<<<<<<< HEAD
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
=======
        $token = $request->bearerToken();

        if ($token) {
            $user = User::where('remember_token', $token)->first();

            if ($user) {
                $user->remember_token = null;
                $user->save();

                return response()->json(['message' => 'Logged out'], 200);
            }

            return response()->json(['message' => 'Invalid token'], 400);
        }

        return response()->json(['message' => 'No token provided'], 400);
>>>>>>> salvando-commit
    }

}
