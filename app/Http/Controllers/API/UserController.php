<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Objectif;
use App\Models\filiere;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Appliquer l'authentification à toutes les méthodes du contrôleur
    }
    
    //public function saveObjectif(Request $request)
//{
    //$user = Auth::user();
    //$selectedOption = $request->input('selected_objectif'); 
    //$objectif = Objectif::where('libelle', $selectedOption)->first();
    
    //if ($objectif) {
        // Associez l'objectif à l'utilisateur
        //$user->objectif_id = $objectif->id;
        //$user->save();
        //return response()->json(['message' => 'Objectif saved successfully']);
    //} else {
        //return response()->json(['error' => 'Objectif not found'], 404);
    //}
//}

public function choisirObjectif(Request $request)
{
    // Assurez-vous que l'utilisateur est authentifié
    if (Auth::check()) {
        // Récupérez l'utilisateur actuellement authentifié
        $user = Auth::user();
        
        // Récupérez la filière choisie à partir de la demande
        $selectedObjectif= $request->input('selected_objectif');
        
        // Recherchez la filière dans la base de données
        $objectif = Objectif::where('libelle', $selectedObjectif)->first();

        if ($objectif ) {
            // Associez l'ID de la filière à l'utilisateur
            $user->objectif_id = $objectif ->id;
            $user->save();

            // Retournez la réponse JSON avec le libellé de la filière
            return response()->json([
                'success' => true,
                'message' => 'objectif enregistrée avec succès',
                'filiere' => $objectif->libelle, // Ajoutez le libellé de la filière ici
            ], 200);
        } 
    } else {
        return response()->json([
            'success' => false,
            'error' => 'L\'utilisateur n\'est pas authentifié',
        ], 401);
    }
}

public function choisirFiliere(Request $request)
{
    // Assurez-vous que l'utilisateur est authentifié
    if (Auth::check()) {
        // Récupérez l'utilisateur actuellement authentifié
        $user = Auth::user();
        
        // Récupérez la filière choisie à partir de la demande
        $selectedFiliere = $request->input('selected_filiere');
        
        // Recherchez la filière dans la base de données
        $filiere = Filiere::where('libelle', $selectedFiliere)->first();

        if ($filiere) {
            // Associez l'ID de la filière à l'utilisateur
            $user->filiere_id = $filiere->id;
            $user->save();

            // Retournez la réponse JSON avec le libellé de la filière
            return response()->json([
                'success' => true,
                'message' => 'Filière enregistrée avec succès',
                'filiere' => $filiere->libelle, // Ajoutez le libellé de la filière ici
            ], 200);
        } 
    } else {
        return response()->json([
            'success' => false,
            'error' => 'L\'utilisateur n\'est pas authentifié',
        ], 401);
    }
}
//public function savefiliere(Request $request)
//{
    //$this->middleware('auth:sanctum');
    // Assurez-vous que l'utilisateur est authentifié
    //if (Auth::check()) {
        //$user = Auth::user();
        //$selectedOption = $request->input('selected_filiere'); 

        //$filiere = filiere::where('libelle', $selectedOption)->first();

        ///if ($filiere) {
         //Associez l'ID de la filière à l'utilisateur
            //$user->filiere_id = $filiere->id;
            //$user->save();

            //return response()->json(['filiere_id' => $filiere->id]);
        //} else {
            //return response()->json(['error' => 'filiere not found'], 404);
        //}
    //} else {
        //return response()->json(['error' => 'User not authenticated'], 401);
    //}
//}
}
