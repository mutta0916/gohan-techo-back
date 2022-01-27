<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//後で消す
use Illuminate\Support\Facades\Log;
use App\Recipe;
use App\RecipeGenre;
use App\RecipeHowto;
use App\RecipeType;
use SebastianBergmann\Environment\Console;

class RecipeController extends Controller
{
    public function index()
    {
        $genres = RecipeGenre::all();
        $types = RecipeType::all();
        return response()->json([
            'message' => 'OK!',
            'genres' => $genres,
            'types' => $types
        ], 200);
    }

    public function store(Request $request)
    {
        // 料理テーブル更新
        $recipe = new Recipe;
        $recipeParams = $request->only(['name', 'genre_id', 'type_id', 'servings', 'memo']);
        $recipeParams = array_merge($recipeParams,array('user_id' => 1));
        Log::info($recipeParams);
        $recipe->fill($recipeParams)->save();

        // 料理手順テーブル更新
        $recipeHowto = new RecipeHowto;
        $recipeId = $recipe->id;
        $arrHowto = $request->input('howto');
        foreach($arrHowto as $value){
          $howtoParams = array('user_id' => 1, 'recipe_id' => $recipeId, 'howto_id' => $value['id'], 'howto' => $value['howto']);
          $recipeHowto->fill($howtoParams)->save();
        }

        return response()->json([
            'message' => 'Recipe created successfully'
        ], 201);
    }
}
