<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//後で消す
use Illuminate\Support\Facades\Log;
use App\Recipe;
use SebastianBergmann\Environment\Console;

class RecipeController extends Controller
{
    public function index()
    {
        $message = 'hello!api!';

        return response()->json([
            'message' => $message
        ]);
    }

    public function store(Request $request)
    {
        $recipe = new Recipe;
        // $form = $request->all();
        //$form = $request->input('name');
        $recipe->user_id = 1;
        $recipe->name = $request->input('name');
        Log::info('★★★登録する中身だよ！★★★');
        // $recipe->fill($form)->save();
        $recipe->save();
        $recipes = Recipe::all();
        return response()->json([
            'message' => 'Recipe created successfully',
            'data' => $recipes
        ], 201);
    }
}
