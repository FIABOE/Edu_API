<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class ProfilController extends Controller
{
    /**
     * Store the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Valider les données de la demande
    $validator = Validator::make($request->all(), [
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'pseudo' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

 
    $user = auth()->user();

    if (!$user) {
        // L'utilisateur n'est pas authentifié, gérer l'erreur en conséquence
        return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
    }
    try {

        // Rechercher s'il existe déjà un profil pour cet utilisateur
        $profil = Profil::where('user_id', $user->id)->first();

        if (!$profil) {
            // Si aucun profil n'existe, créez-en un nouveau
            $profil = new Profil();
            $profil->user_id = $user->id;
        }
        // Gérer le téléchargement de l'avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('avatars', 'public');
            $profil->avatar = $avatarPath;
        }

        // Mettre à jour le champ "pseudo" dans le profil
        $profil->pseudo = $request->input('pseudo');
        $profil->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'data' => $profil,
        ], 200);
    } catch (\Exception $e) {
        // Enregistrer l'erreur dans les journaux
        error_log($e);
        
        return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour du profil.'], 500);
    }
}

    public function getUserProfile(Request $request)
    {
        // Récupérez l'utilisateur authentifié à partir du token d'authentification
        $user = $request->user();

        if ($user) {
            // L'utilisateur est authentifié, récupérez les informations du profil
            $profil = $user->profil;

            if ($profil) {
                // Retournez les données du profil
                return response()->json([
                    'success' => true,
                    'pseudo' => $profil->pseudo,
                    'avatar' => $profil->avatar,
                ]);
            }
        }

        // Si l'utilisateur n'est pas trouvé ou s'il n'a pas de profil, retournez une réponse appropriée
        return response()->json([
            'success' => false,
            'message' => 'Profil introuvable',
        ], 404);
    }

}