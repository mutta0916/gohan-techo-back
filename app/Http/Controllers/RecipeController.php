<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//後で消す
use Illuminate\Support\Facades\Log;
use App\Recipe;
use App\RecipeGenre;
use App\RecipeHowto;
use App\RecipeIngredient;
use App\RecipeType;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Storage;

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
        $userId = $request->input('user_id');
        $recipeParams = $request->only(['name', 'genre_id', 'type_id', 'servings', 'memo']);
        $recipeParams = array_merge($recipeParams,array('user_id' => 1));
        $recipe->fill($recipeParams)->save();

        // 料理手順テーブル更新
        $recipeId = $recipe->id;
        $arrHowto = json_decode($request->input('howto'));
        foreach($arrHowto as $value){
          $recipeHowto = new RecipeHowto;
          $howtoParams = array('user_id' => $userId, 'recipe_id' => $recipeId, 'howto_id' => $value->id, 'howto' => $value->howto);
          $recipeHowto->fill($howtoParams)->save();
        }

        // 料理材料テーブル更新
        $arrIngredient = json_decode($request->input('ingredient'));
        foreach($arrIngredient as $value){
          $recipeIngredient = new RecipeIngredient;
          $ingredientParams = array('user_id' => $userId, 'recipe_id' => $recipeId, 'ingredient_id' => $value['id'], 'name' => $value['name'], 'amount' => $value['amount']);
          $recipeIngredient->fill($ingredientParams)->save();
        }

        // 料理写真取得
        $photo = $request->file('photo');
        $file_name = sprintf('%s%s%s', $userId, '/', $recipeId);
        $disk = Storage::disk('s3');
        $disk->put($file_name, $photo, 'public');

        return response()->json([
            'message' => 'Recipe created successfully'
        ], 201);
    }
}
