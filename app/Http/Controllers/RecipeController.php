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
        $recipes = Recipe::where('user_id', $userId)->get();
        $returnRecipes = $this->getPhoto($recipes, $userId);
        return response()->json([
            'message' => 'OK!',
            'recipes' => $returnRecipes
        ], 200);
    }

    public function show($recipeId)
    {
      $id = 1;
      $recipe = Recipe::find($recipeId);
      $howtos = $recipe->howtos()->get();
      $ingredients = $recipe->ingredients()->get();
      $recipe = array($recipe);
      $returnRecipe = $this->getPhoto($recipe, $id);
      return response()->json([
          'message' => 'OK!',
          'recipes' => $returnRecipe,
          'howtos' => $howtos,
          'ingredients' => $ingredients
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

        // 料理写真登録
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
        foreach($arrHowto as $value){
          RecipeHowto::updateOrCreate(
            ['id' => $value->id],
            ['recipe_id' => $recipeId, 'howto' => $value->howto]
          );
        }

        // 料理材料テーブル更新
        $arrIngredient = json_decode($request->input('ingredient'));
        foreach($arrIngredient as $value){
          RecipeIngredient::updateOrCreate(
            ['id' => $value->id],
            ['recipe_id' => $recipeId, 'name' => $value->name, 'amount' => $value->amount]
          );
        }

        // 料理写真登録
        $photo = $request->file('photo');
        $disk = Storage::disk('s3');
        $path = sprintf('%s%s%s%s%s', 'userId=', 1, '/', 'recipeId=', $recipeId, '/');
        $files = $disk->allFiles($path);
        $disk->delete($files);
        $disk->put($path, $photo, 'public');

        return response()->json([
            'message' => 'Recipe updated successfully'
        ], 201);
    }

    public function getPhoto($recipes, $userId)
    {
        $returnRecipes = array();
        $disk = Storage::disk('s3');
        foreach($recipes as $value){
          $path = sprintf('%s%s%s%s%s%s', 'userId=', $userId, '/', 'recipeId=', $value->id, '/');
          $files = $disk->allFiles($path);
          if(count($files) > 0) {
            $contents = $disk->get($files[0]);
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
              'genre' => RecipeGenre::find($value->genre_id)->first()->genre,
              'type' => RecipeType::find($value->type_id)->first()->type,
              'memo' => $value->memo,
              'photo' => $contents
          ));
       }
       return $returnRecipes;
    }
}
