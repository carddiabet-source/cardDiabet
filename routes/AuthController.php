<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        // 1. Tenta encontrar o usuário pelo CRM
        $user = User::where('crm', $crm)->first();

        // 2. Se o usuário não existir, retorna erro 404
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        // 3. Tenta autenticar com as credenciais
        if (!auth()->attempt($request->only('crm', 'password'))) {
            return response()->json(['message' => 'Senha inválida.'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}
