<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Cours;
use App\Models\filiere;
use Illuminate\Validation\Rule;


class CoursController extends Controller
{
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'filiere_id' => ['required_without:filiere_libelle', 'exists:filieres,id'],
        'filiere_libelle' => ['required_without:filiere_id'],
        'pdf_file' => ['required', 'mimes:pdf'],
        // Autres règles de validation...
    ], [
        'filiere_id.required_without' => 'L\'ID de la filière ou le libellé de la filière est requis.',
        'filiere_id.exists' => 'L\'ID de la filière n\'existe pas dans la base de données.',
        'filiere_libelle.required_without' => 'L\'ID de la filière ou le libellé de la filière est requis.',
        'pdf_file.required' => 'Le fichier PDF est requis.',
        'pdf_file.mimes' => 'Le fichier doit être un fichier PDF.',
        // Messages d'erreur pour les autres règles...
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $data = $validator->validated();

    if (array_key_exists('filiere_libelle', $data)) {
        // Si le libellé de la filière est fourni, recherchez l'ID correspondant
        $filiere = Filiere::where('libelle', $data['filiere_libelle'])->first();
        if (!$filiere) {
            return response()->json(['error' => 'La filière spécifiée n\'existe pas.'], 400);
        }
        $data['filiere_id'] = $filiere->id;
        unset($data['filiere_libelle']); // Supprimez le libellé de la filière pour éviter une erreur
    }

    // Effectuez la création du cours à ce niveau, après avoir géré le libellé de la filière
    $pdfFile = $request->file('pdf_file');
    $pdfFileName = time() . '_' . $pdfFile->getClientOriginalName();

    if (Storage::disk('public')->exists('pdf_cours/' . $pdfFileName)) {
        return response()->json(['error' => 'Ce fichier PDF existe déjà dans la base de données.'], 400);
    }

    $existingCours = Cours::where('filiere_id', $data['filiere_id'])
        ->where('pdf_file', 'pdf_cours/' . $pdfFileName)
        ->first();

    if ($existingCours) {
        return response()->json(['error' => 'Ce cours existe déjà pour cette filière.'], 400);
    }

    $pdfFile->storeAs('pdf_cours', $pdfFileName, 'public');

    $cours = Cours::create([
        'pdf_file' => 'pdf_cours/' . $pdfFileName,
        'pdf_file_name' => $pdfFile->getClientOriginalName(),
        'filiere_id' => $data['filiere_id'],
    ]);

    if ($cours) {
        return response()->json(['message' => 'Cours ajouté avec succès.', 'cours' => $cours], 201);
    } else {
        return response()->json(['error' => 'Échec de la création du cours.'], 500);
    }
}





    public function update(Request $request, $id)
    {
        // Recherchez le cours par ID
        $cours = Cours::find($id);

        if (!$cours) {
            return response()->json(['error' => 'Cours non trouvé'], 404);
        }

        // Validation des données de mise à jour
        $validator = Validator::make($request->all(), [
            'courName' => 'required|string|max:255',
            'pdf_file' => [
                'required',
                'mimes:pdf',
                Rule::unique('cours', 'pdf_file')->ignore($cours->id),
            ],
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Vérifier si un nouveau fichier PDF est téléchargé
        if ($request->hasFile('pdf_file')) {
            // Obtenir le fichier PDF téléchargé
            $pdfFile = $request->file('pdf_file');

            // Générer un nom de fichier unique
            $pdfFileName = time() . '_' . $pdfFile->getClientOriginalName();

            // Stocker le fichier PDF dans le dossier public/pdf_cours
            $pdfFile->storeAs('pdf_cours', $pdfFileName, 'public');

            // Mettre à jour les données du cours
            $cours->courName = $request->input('courName');
            $cours->pdf_file = 'pdf_cours/' . $pdfFileName;
            $cours->filiere_id = $request->input('filiere_id');
        } else {
            // Si aucun nouveau fichier PDF n'est téléchargé, mettez à jour les autres données
            $cours->courName = $request->input('courName');
            $cours->filiere_id = $request->input('filiere_id');
        }

        // Enregistrez les modifications du cours
        if ($cours->save()) {
            return response()->json(['message' => 'Cours mis à jour avec succès', 'cours' => $cours], 200);
        } else {
            // En cas d'erreur lors de la mise à jour du cours
            return response()->json(['error' => 'Échec de la mise à jour du cours'], 500);
        }
    }

    public function destroy($id)
    {
        // Recherchez le cours par ID
        $cours = Cours::find($id);

        if (!$cours) {
            return response()->json(['error' => 'Cours non trouvé'], 404);
        }

        // Supprimez le fichier PDF associé, s'il existe
        if (Storage::disk('public')->exists($cours->pdf_file)) {
            Storage::disk('public')->delete($cours->pdf_file);
        }

        // Supprimez le cours de la base de données
        $cours->delete();

        return response()->json(['message' => 'Cours supprimé avec succès'], 200);
    }

}
    

    
    