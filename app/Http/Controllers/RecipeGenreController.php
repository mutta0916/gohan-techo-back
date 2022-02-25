<?php

namespace App\Http\Controllers;

use App\RecipeGenre;

class RecipeGenreController extends Controller
{
    public function index()
    {
        $genres = RecipeGenre::all();
        return response()->json([
            'message' => 'OK!',
            'genres' => $genres
        ], 200);
    }
}
