<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login'); // retorna o Blade do login
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'crm' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // garante CRM em maiúsculo
        $credentials['crm'] = strtoupper(trim($credentials['crm']));

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Login realizado com sucesso!');
        }

        return back()->withErrors([
            'crm' => 'CRM ou senha inválidos',
        ])->onlyInput('crm');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout realizado com sucesso!');
    }
}