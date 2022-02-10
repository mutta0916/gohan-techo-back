<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//後で消す
use Illuminate\Support\Facades\Log;
use App\Recipe;
use App\RecipeHowto;
use App\RecipeIngredient;
use App\RecipeGenre;
use App\RecipeType;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $recipes = Recipe::where('user_id', $userId)
        ->get(['id', 'user_id', 'name', 'genre_id', 'type_id', 'memo']);
        $returnRecipes = $this->getPhoto($recipes, $userId);
        return response()->json([
            'message' => 'OK!',
            'recipes' => $returnRecipes
        ], 200);
    }

    public function show($recipeId)
    {
      $id = 1;
      $recipe = Recipe::find($recipeId)
      ->get(['id', 'user_id', 'name', 'genre_id', 'type_id', 'memo', 'servings']);
      $howtos = Recipe::find($recipeId)->howtos()->get(['id', 'howto']);
      $returnRecipe = $this->getPhoto($recipe, $id);
      return response()->json([
          'message' => 'OK!',
          'recipes' => $returnRecipe,
          'howtos' => $howtos,
      ], 200);
    }

    // public function index()
    // {
    //     $genres = RecipeGenre::all();
    //     $types = RecipeType::all();
    //     return response()->json([
    //         'message' => 'OK!',
    //         'genres' => $genres,
    //         'types' => $types
    //     ], 200);
    // }

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

    public function update(Request $request, $recipeId)
    {
        // 料理テーブル更新
        $recipe = Recipe::find($recipeId);
        $recipeParams = $request->only(['name', 'genre_id', 'type_id', 'servings', 'memo']);
        $recipe->fill($recipeParams)->save();

        // 料理手順テーブル更新
        $arrHowto = json_decode($request->input('howto'));
        Log::info($arrHowto);
        foreach($arrHowto as $value){
          Log::info($value->howto);
          RecipeHowto::updateOrCreate(
            ['id' => $value->id],
            ['recipe_id' => $recipeId, 'howto' => $value->howto]
          );
        }

        // // 料理材料テーブル更新
        // $arrIngredient = json_decode($request->input('ingredient'));
        // foreach($arrIngredient as $value){
        //   $recipeIngredient = new RecipeIngredient;
        //   $ingredientParams = array('user_id' => $userId, 'recipe_id' => $recipeId, 'ingredient_id' => $value['id'], 'name' => $value['name'], 'amount' => $value['amount']);
        //   $recipeIngredient->fill($ingredientParams)->save();
        // }

        // // 料理写真取得
        // $photo = $request->file('photo');
        // $file_name = sprintf('%s%s%s', $userId, '/', $recipeId);
        // $disk = Storage::disk('s3');
        // $disk->put($file_name, $photo, 'public');

        return response()->json([
            'message' => 'Recipe created successfully'
        ], 201);
    }

    public function getPhoto($recipes, $userId)
    {
        $returnRecipes = array();
        $disk = Storage::disk('s3');
        foreach($recipes as $value){
          $path = sprintf('%s%s%s%s%s', $userId, '/', $value->id, '/', 'fjJueycKi2NAuz9W5w9Aqln0epclONZVx8Qh1JF1.jpg');
          if($disk->exists($path)) {
              $contents = $disk->get($path);
              $contents = sprintf('%s%s', 'data:image/jpeg;base64,', base64_encode($contents));
          } else {
              $contents = null;
          }
          array_push($returnRecipes,
          array
          (
              'id' => $value->id,
              'user_id' => $userId,
              'name' => $value->name,
              'genre' => RecipeGenre::where('id', $value->genre_id)->first()->genre,
              'type' => RecipeType::where('id', $value->type_id)->first()->type,
              'memo' => $value->memo,
              'photo' => $contents
          ));
       }
       return $returnRecipes;
    }
}
