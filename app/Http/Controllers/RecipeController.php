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
        Log::info('★★★登録処理です★★★');
        $recipe = new Recipe;
        $recipe->user_id = 1;
        $recipe->name = $request->input('name');
        $recipe->genre_id = $request->input('genre');
        $recipe->type_id = $request->input('type');
        $recipe->servings = $request->input('servings');
        $recipe->memo = $request->input('memo');
        $recipe->save();

        // 料理手順テーブルデータ追加
        $id = $recipe->id;
        $recipeHowto = new RecipeHowto;
        $arrHowto = $request->input('howto');
        Log::info($arrHowto);
        foreach($arrHowto as $value){
          Log::info('配列取得');
          Log::info($value);
          Log::info('ID取得');
          Log::info($value->id);
          Log::info('作り方取得');
          Log::info($value->howto);
          $recipeHowto->user_id = 1;
          $recipeHowto->recipe_id = $id;
          $recipeHowto->howto_id = $value->id;
          $recipeHowto->howto = $value;
          // $recipeHowto->save();
        }

        // $recipe->fill($form)->save();

        $recipes = Recipe::all();
        return response()->json([
            'message' => 'Recipe created successfully',
            'data' => $recipes
        ], 201);
    }
}
