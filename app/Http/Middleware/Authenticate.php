<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            // Si la requête attend une réponse JSON, renvoyez une réponse JSON d'erreur personnalisée
            return null;
        } else {
            // Si la requête n'attend pas une réponse JSON, effectuez une redirection personnalisée
            // Vous pouvez remplacer 'login' par l'URL de redirection de votre choix
            return route('login');
        }
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            // Réponse JSON d'erreur personnalisée pour les requêtes JSON
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        // Redirection personnalisée pour les autres requêtes
        return redirect()->route('login');
    }
}

