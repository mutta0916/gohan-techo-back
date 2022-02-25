<?php

namespace App\Http\Controllers;

use App\RecipeType;

class RecipeTypeController extends Controller
{
    public function index()
    {
        $types = RecipeType::all();
        return response()->json([
            'message' => 'OK!',
            'types' => $types
        ], 200);
    }
}
