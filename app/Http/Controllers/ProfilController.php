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
    
    // Récupérer l'utilisateur authentifié
    
    $user = Auth::user();
    
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
                return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour du profil.'], 500);
        }
    }

}