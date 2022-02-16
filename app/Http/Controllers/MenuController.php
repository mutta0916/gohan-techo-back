<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use App\MenuRecipe;

class MenuController extends Controller
{
  public function index(Request $request)
  {
      // $userId = $request->input('user_id');
      // $recipes = Recipe::where('user_id', $userId)->get();
      // $returnRecipes = $this->getPhoto($recipes, $userId);
      // return response()->json([
      //     'message' => 'OK!',
      //     'recipes' => $returnRecipes
      // ], 200);
  }

  public function show($recipeId)
  {
    // $id = 1;
    // $recipe = Recipe::find($recipeId);
    // $howtos = $recipe->howtos()->get();
    // $ingredients = $recipe->ingredients()->get();
    // $recipe = array($recipe);
    // $returnRecipe = $this->getPhoto($recipe, $id);
    // return response()->json([
    //     'message' => 'OK!',
    //     'recipes' => $returnRecipe,
    //     'howtos' => $howtos,
    //     'ingredients' => $ingredients
    // ], 200);
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
      // 献立テーブル更新
      $menu = new Menu;
      $menuParams = $request->only(['user_id', 'date', 'category', 'title']);
      $menuParams = array_merge($menuParams,array('user_id' => 1));
      $menu->fill($menuParams)->save();

      // 献立明細テーブル更新
      $menuRecipe = new MenuRecipe;
      $menuId = $menu->id;
      $menuParams = $request->only(['location', 'recipe_id']);
      $menuParams = array_merge($menuParams,array('menu_id' => $menuId));
      $menuRecipe->fill($menuParams)->save();

      return response()->json([
          'message' => 'Menu created successfully'
      ], 201);
  }

  public function update(Request $request, $recipeId)
  {
  //     // 料理テーブル更新
  //     $recipe = Recipe::find($recipeId);
  //     $recipeParams = $request->only(['name', 'genre_id', 'type_id', 'servings', 'memo']);
  //     $recipe->fill($recipeParams)->save();

  //     // 料理手順テーブル更新
  //     $arrHowto = json_decode($request->input('howto'));
  //     foreach($arrHowto as $value){
  //       RecipeHowto::updateOrCreate(
  //         ['id' => $value->id],
  //         ['recipe_id' => $recipeId, 'howto' => $value->howto]
  //       );
  //     }

  //     // 料理材料テーブル更新
  //     $arrIngredient = json_decode($request->input('ingredient'));
  //     foreach($arrIngredient as $value){
  //       RecipeIngredient::updateOrCreate(
  //         ['id' => $value->id],
  //         ['recipe_id' => $recipeId, 'name' => $value->name, 'amount' => $value->amount]
  //       );
  //     }

  //     // 料理写真登録
  //     $photo = $request->file('photo');
  //     if ($photo) {
  //         $disk = Storage::disk('s3');
  //         $path = sprintf('%s%s%s%s%s%s', 'userId=', 1, '/', 'recipeId=', $recipeId, '/');
  //         $files = $disk->allFiles($path);
  //         $disk->delete($files);
  //         $disk->put($path, $photo, 'public');
  //     }

  //     return response()->json([
  //         'message' => 'Recipe updated successfully'
  //     ], 201);
  // }

  // public function getPhoto($recipes, $userId)
  // {
  //     $returnRecipes = array();
  //     $disk = Storage::disk('s3');
  //     foreach($recipes as $value){
  //       $path = sprintf('%s%s%s%s%s%s', 'userId=', $userId, '/', 'recipeId=', $value->id, '/');
  //       $files = $disk->allFiles($path);
  //       if(count($files) > 0) {
  //         $contents = $disk->get($files[0]);
  //         $contents = sprintf('%s%s', 'data:image/jpeg;base64,', base64_encode($contents));
  //       } else {
  //         $contents = null;
  //       }
  //       array_push($returnRecipes,
  //       array
  //       (
  //           'id' => $value->id,
  //           'user_id' => $userId,
  //           'name' => $value->name,
  //           'genre' => RecipeGenre::find($value->genre_id)->first()->genre,
  //           'type' => RecipeType::find($value->type_id)->first()->type,
  //           'memo' => $value->memo,
  //           'photo' => $contents
  //       ));
  //    }
  //    return $returnRecipes;
  }

  public function destroy($recipeId)
  {
    //   // 料理テーブル更新
    //   Recipe::find($recipeId)->delete();

    //   // 料理手順テーブル更新
    //   RecipeHowto::where('recipe_id', $recipeId)->delete();

    //   // 料理材料テーブル更新
    //   RecipeIngredient::where('recipe_id', $recipeId)->delete();

    //   // 料理写真登録
    //   $disk = Storage::disk('s3');
    //   $path = sprintf('%s%s%s%s%s%s', 'userId=', 1, '/', 'recipeId=', $recipeId, '/');
    //   $files = $disk->allFiles($path);
    //   $disk->delete($files);

    //   return response()->json([
    //     'message' => 'Recipe deleted successfully'
    // ], 204);
  }
}
