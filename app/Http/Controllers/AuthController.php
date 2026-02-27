<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'crm' => ['required', 'string', 'min:4', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $crm = strtoupper(trim($request->crm));
        $auth = Auth::attempt([
            'crm' => $crm,
            'password' => $request->password
        ]);

        if ($auth) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Login successful']);
        }
        return response()->json([
            'message' => 'CRM ou senha inválidos'
        ], 401);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}
