<?php

namespace App\Http\Controllers;

use App\Models\Branche;
use App\Models\Compagnie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "last_name" => "required|string",
            "email" => "required|string",
            "compagnie_name" => "required|string",
            "password" => "required|string",
        ]);

        return DB::transaction(function () use ($request, &$user) {

            // 1. Créer l'entreprise
            $compagnie = Compagnie::create([
                "name" => $request->compagnie_name,
                "slug" => Str::slug($request->compagnie_name)
            ]);
            // 2. Créer la branche principale
            $branch = Branche::create([
                "compagnie_id" => $compagnie->id,
                "name" => "Dépot Principal",
                "location" => "Siège social"
            ]);

            // 3. Créer l'utilisateur avec le rôle admin
            // Le fondateur de la compagnie est toujours admin
            // Crééation de l'utilisateur
            $user = User::create([
                "name" => $request->name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "compagnie_id" => $compagnie->id,
                "password" => $request->password,
                "role" => "admin"
            ]);
            // 4. Attacher l'admin à la branche principale via la pivot
            // Même si l'admin voit tout via compagnie_id,
            // on l'attache pour garder une trace de son dépôt "home"
            $user->branches()->attach($branch->id);

            return response()->json([
                'token' => $user->createToken('auth:token')->plainTextToken,
                'user' => $user->load('branches'),
            ]);
        });

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // dd($request);
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email ou mot de passe invalide'], 401);
        }
        // $request->session()->regenerate();
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'message' => 'Login succesful',
            'user' => $user->load(['branches', 'compagnie']),
            'token' => $token
        ], 200);
    }
}
