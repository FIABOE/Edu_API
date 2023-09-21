<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Objectif;
use Illuminate\Support\Facades\Validator;

class ObjectifController extends Controller
{
    public function store(Request $request)
    {
        // Définissez les messages d'erreur personnalisés
        $messages = [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne doit pas dépasser :max caractères.',
            'libelle.unique' => 'Cet objectif existe déjà.',
        ];
    
        // Définissez les règles de validation
        $rules = [
            'libelle' => 'required|string|max:255|unique:objectifs,libelle',
        ];
    
        // Créez le validateur avec les règles et les messages personnalisés
        $validator = Validator::make($request->all(), $rules, $messages);
    
        // Vérifiez si la validation a échoué
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }
    
        try {
            // Créer un nouvel objectif avec les données validées
            $objectif = Objectif::create([
                'libelle' => $request->input('libelle'),
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Objectif ajouté avec succès',
                'data' => $objectif,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de l\'objectif',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
{
    $objectif = Objectif::find($id);

    if (!$objectif) {
        return response()->json(['success' => false, 'message' => 'Objectif non trouvé'], 404);
    }

    // Définissez les règles de validation pour la mise à jour
    $rules = [
        'libelle' => 'required|string|max:255|unique:objectifs,libelle,' . $objectif->id,
    ];

    // Créez le validateur avec les règles
    $validator = Validator::make($request->all(), $rules);

    // Vérifiez si la validation a échoué
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $validator->errors(),
        ], 400);
    }

    try {
        // Mettez à jour l'objectif avec les données validées
        $objectif->update([
            'libelle' => $request->input('libelle'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objectif mis à jour avec succès',
            'data' => $objectif,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour de l\'objectif',
            'error' => $e->getMessage(),
        ], 400);
    }
}

public function destroy($id)
{
    $objectif = Objectif::find($id);

    if (!$objectif) {
        return response()->json(['success' => false, 'message' => 'Objectif non trouvé'], 404);
    }

    try {
        $objectif->delete();

        return response()->json(['success' => true, 'message' => 'Objectif supprimé avec succès'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression de l\'objectif',
            'error' => $e->getMessage(),
        ], 400);
    }
}

public function index()
{
    // Récupérez toutes les filières de la base de données
    $objectifs = Objectif::all();

    // Vérifiez si la liste des filières est vide
    if ($objectifs->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'Aucun élément trouvé.',
            'data' => [],
        ], 200);
    }

    // Retournez la liste des filières en tant que réponse JSON
    return response()->json([
        'success' => true,
        'data' => $objectifs,
    ], 200);
}


}
    
