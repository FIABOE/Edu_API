<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\filiere;
use Illuminate\Support\Facades\Validator; // Importez la classe Validator
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class QuizController extends Controller
{
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'filiere_id' => ['required_without:filiere_libelle', 'exists:filieres,id'],
        'filiere_libelle' => ['required_without:filiere_id',],
        'question' => 'required',
        'options' => 'required|array',
        'options.*' => 'string',
        'correct_option' => [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                $options = $request->input('options');
        
                // Vérifiez d'abord que 'options' est un tableau
                if (!is_array($options)) {
                    $fail("Les options de réponse doivent être fournies sous forme de tableau.");
                    return;
                }
        
                if (!in_array($value, $options)) {
                    $fail("La réponse correcte doit être l'une des options fournies.");
                }
            },
        ],  
        'type_quiz' => [
            'required',
            //Rule::in(['qcm', 'vrai_ou_faux']),
        ],
    ], [
        'filiere_id.required_without' => 'L\'ID de la filière ou le libellé de la filière est requis.',
        'filiere_id.exists' => 'L\'ID de la filière n\'existe pas dans la base de données.',
        'filiere_libelle.required_without' => 'L\'ID de la filière ou le libellé de la filière est requis.',
        //'filiere_libelle.string' => 'Le libellé de la filière doit être une chaîne de caractères.',
        'question.required' => 'La question est requise.',
        'options.required' => 'Les options de réponse sont requises.',
        'options.array' => 'Les options de réponse doivent être fournies sous forme de tableau.',
        'options.*.string' => 'Chaque option de réponse doit être une chaîne de caractères.',
        'correct_option.required' => 'La réponse correcte est requise.',
        'correct_option.*' => 'La "correct_option" doit être l\'une des options fournies.',
        'type_quiz.required' => 'Le type de quiz est requis.',
        //'type_quiz.in' => 'Le type de quiz doit être soit "qcm" (Question à Choix Multiples) soit "vrai_ou_faux".',
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

    try {
        $quiz = Quiz::create($data);
    } catch (QueryException $e) {
        if ($e->errorInfo[1] === 1062) {
            return response()->json(['error' => 'Ce quiz existe déjà dans la base de données.'], 400);
        }
    }

    if ($quiz) {
        return response()->json(['message' => 'Quiz créé avec succès', 'quiz' => $quiz], 201);
    } else {
        return response()->json(['error' => 'Échec de la création du quiz'], 500);
    }
}




    public function destroy($id)
    {
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return response()->json(['error' => 'Quiz non trouvé'], 404);
        }
        $quiz->delete();
        return response()->json(['message' => 'Quiz supprimé avec succès'], 200);
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        if (!$quiz) {
            return response()->json(['error' => 'Quiz non trouvé'], 404);
        }
        // Validez les données de mise à jour (vous pouvez réutiliser votre validation actuelle ici)

        $validator = Validator::make($request->all(), [
            'filiere_id' => 'required|exists:filieres,id',
            'question' => 'required',
            'options' => 'required|array',
            'options.*' => 'string',
            'correct_option' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $options = $request->input('options');
            
                    if (is_array($options) && !empty($options) && !in_array($value, $options)) {
                        $fail("La réponse correcte doit être l'une des options fournies.");
                    }
                },
            ],
            
            'type_quiz' => [
                'required',
                Rule::in(['qcm', 'vrai_ou_faux']),
            ],
        ], [
        // Messages d'erreur personnalisés ici
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $data = $validator->validated();
        $quiz->update($data);
        return response()->json(['message' => 'Quiz mis à jour avec succès', 'quiz' => $quiz], 200);
    }

    public function index()
{
    // Récupérez toutes les filières de la base de données
    $quizs = Quiz::all();

    // Vérifiez si la liste des filières est vide
    if ($quizs->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'Aucun élément trouvé.',
            'data' => [],
        ], 200);
    }

    // Retournez la liste des filières en tant que réponse JSON
    return response()->json([
        'success' => true,
        'data' => $quizs,
    ], 200);
}


public function getQuizByFiliere($filiere_id)
{
    try {
        $quizs = Quiz::where('filiere_id', $filiere_id)->get();

        if ($quizs->isEmpty()) {
            info('Aucun quiz trouvé pour cette filière.');
            return response()->json([
                'success' => true,
                'message' => 'Aucun quiz trouvé pour cette filière.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => $quizs,
        ], 200);
    } catch (\Exception $e) {
        error_log($e);
        // Gérez l'erreur ici
        // Vous pouvez enregistrer l'erreur, envoyer une réponse d'erreur personnalisée, etc.
        return response()->json([
            'success' => false,
            'message' => 'Une erreur s\'est produite lors de la récupération des quiz.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



}