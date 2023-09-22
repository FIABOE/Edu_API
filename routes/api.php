<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\NewPasswordController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ObjectifController;
use App\Http\Controllers\ProfilController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Routes publiques (non authentifiées)
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'reset');
});

Route::controller(NewPasswordController::class)->group(function () {
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'reset');
});


Route::post('/register-moderateur', [ModeratorController::class, 'register']);
Route::get('/moderators', [ModeratorController::class, 'getAllModerators']);


// Routes nécessitant l'authentification (utilisateur normal)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        $user = auth()->user();
        return response()->json([
            'user' => $user,
        ]);
    });

   
    //Route::post('/choisir-filiere', 'API\UserController@choisirFiliere');
    Route::post('/choisir-filiere', [UserController::class, 'choisirFiliere']);
    Route::post('/choisir-objectif', [UserController::class, 'choisirObjectif']);


    Route::post('/objectif', [UserController::class, 'saveObjectif']);
    Route::post('/filiere', [UserController::class, 'savefiliere']);
    Route::post('/revokeTokens', [AuthController::class, 'revokeTokens']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete', [AuthController::class, 'deleteAccount']);
    Route::put('/update', [AuthController::class, 'updateUser']);
    Route::resource('filieres', FiliereController::class);
    Route::resource('objectifs', ObjectifController::class);
    Route::resource('cours', CoursController::class);
    Route::resource('quizzes', QuizController::class);
    Route::resource('/profil', ProfilController::class);
});
 