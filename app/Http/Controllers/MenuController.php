<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use App\MenuRecipe;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Location;

class MenuController extends Controller
{
  public function index(Request $request)
  {
    $userId = $request->input('user_id');
    $year = date('Y');
    $month = date('m');

    $registeredMenus = Menu::leftJoin('menu_recipes', 'menus.id', '=', 'menu_recipes.menu_id')
    ->where('user_id', $userId)
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->get();

    // JSON整形処理
    $returnMenus = array();
    // 日付
    for ($day = 1; $day <= date('t'); $day++) {
        // カテゴリー
        for ($category = 0; $category <= 2; $category++) {
            // 位置
            for ($location = 0; $location <= 5; $location++) {
                array_push($returnMenus,
                array
                (
                  "date" => $year . '-' . $month . '-' . sprintf('%02d', $day),
                  "category" => $category,
                  "location" => $location,
                  "title" => "",
                  "recipe_id" => 0
                ));
            }
        }
    }

    // 検索
    foreach ($registeredMenus As $registeredMenu)
    {
        // 更新する配列を取得
        $updateArray = array_filter($returnMenus, function($returnMenu) use($registeredMenu) {
            Log::info('登録済み情報');
            Log::info($registeredMenu->date);
            Log::info($registeredMenu->category);
            Log::info($registeredMenu->location);
            Log::info('枠情報');
            Log::info($returnMenu["date"]);
            Log::info($returnMenu["category"]);
            Log::info($returnMenu["location"]);
            return $returnMenu["date"] === $registeredMenu->date && $returnMenu["category"] === $registeredMenu->category && $returnMenu["location"] === $registeredMenu->location;
        });
        Log::info($updateArray);
        $index = key($updateArray);
        Log::info($index);
        $returnMenus[$index]["title"] = "テスト";
        $returnMenus[$index]["recipe_id"] = $registeredMenu->recipe_id;
    }

    return response()->json([
        'message' => 'OK!',
        'updateArray' => $updateArray,
        'menus' => $returnMenus
    ], 200);
  }

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

  // public function update(Request $request, $recipeId)
  // {
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

  // public function destroy($recipeId)
  // {
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
  //}
}
