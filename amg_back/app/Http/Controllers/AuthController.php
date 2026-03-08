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

            // Créer l'entreprise
            $compagnie = Compagnie::create([
                "name" => $request->compagnie_name,
                "slug" => Str::slug($request->compagnie_name)
            ]);
            // Création de la branche par defaut
            $branch = Branche::create([
                "compagnie_id" => $compagnie->id,
                "name" => "Dépot Principal",
                "location" => "Siège social"
            ]);
            // Crééation de l'utilisateur
            $user = User::create([
                "name" => $request->name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "compagnie_id" => $compagnie->id,
                "password" => $request->password,
                "branche_id"=>$branch->id
            ]);
            return response()->json([
                'token' => $user->createToken('auth:token')->plainTextToken,
                'user' => $user,
                "branche"=> $branch
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
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
