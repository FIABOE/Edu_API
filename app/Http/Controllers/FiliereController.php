<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FiliereController extends Controller
{
    public function store(Request $request)
    {
        // Définission des messages d'erreur personnalisés
        $messages = [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne doit pas dépasser :max caractères.',
            'libelle.unique' => 'Ce libellé de filière existe déjà.',
        ];
        // Définission des règles de validation
        $rules = [
            'libelle' => 'required|string|max:255|unique:filieres,libelle',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
    
        // Vérification de la validation ( échoué ou pas)
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }
        try {
            // Création de  nouvelle filière avec les données validées
            $filiere = Filiere::create([
                'libelle' => $request->input('libelle'),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Filière ajoutée avec succès',
                'data' => $filiere,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la filière',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        // Recherchez la filière par ID
        $filiere = Filiere::find($id);

        if (!$filiere) {
            return response()->json(['success' => false, 'message' => 'Filière non trouvée'], 404);
        }
        // Définissez les règles de validation pour la mise à jour
        $rules = [
            'libelle' => 'required|string|max:255|unique:filieres,libelle,' . $filiere->id,
        ];
        // Créez le validateur pour la mise à jour
        $validator = Validator::make($request->all(), $rules);
        // Vérifiez si la validation a échoué
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }
        // Mettez à jour le libellé de la filière
        $filiere->libelle = $request->input('libelle');
        $filiere->save();

        return response()->json([
            'success' => true,
            'message' => 'Filière mise à jour avec succès',
            'data' => $filiere,
        ], 200);
    }

    public function destroy($id)
    {
        // Recherchez la filière par ID
        $filiere = Filiere::find($id);

        if (!$filiere) {
            return response()->json(['success' => false, 'message' => 'Filière non trouvée'], 404);
        }

        // Supprimez la filière
        $filiere->delete();

        return response()->json(['success' => true, 'message' => 'Filière supprimée avec succès'], 200);
    }
    public function index()
{
    // Récupérez toutes les filières de la base de données
    $filieres = Filiere::all();

    // Vérifiez si la liste des filières est vide
    if ($filieres->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'Aucun élément trouvé.',
            'data' => [],
        ], 200);
    }
    
    // Retournez la liste des filières en tant que réponse JSON
    return response()->json([
        'success' => true,
        'data' => $filieres,
    ], 200);
}


}
    
    

