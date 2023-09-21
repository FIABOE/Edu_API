<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

class ModeratorController extends Controller
{
    public function register(Request $request)
    {
        // Créez un nouvel utilisateur avec le rôle "moderateur"
        $user = new User;
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->role = 'moderateur'; 
        // Sauvegardez l'utilisateur dans la base de données
        $user->save();
        
        // Génération du jeton d'accès
        $token = $user->createToken('Myapp')->plainTextToken;
        // Associer le jeton à l'utilisateur dans la base de données
        $user->update(['remember_token' => $token]);
        // Retournez une réponse JSON avec le token
        return response()->json([
            'success' => true,
            'message' => 'Moderator registered successfully',
            'token' => $token 
        ]);
    }

    public function getAllModerators()
    {
        // Sélectionnez tous les utilisateurs ayant le rôle "moderateur"
        $moderators = User::where('role', 'moderateur')->get();

        // Retournez la liste des modérateurs sous forme de réponse JSON
        return response()->json([
            'success' => true,
            'moderators' => $moderators,
        ]);
    }

}

