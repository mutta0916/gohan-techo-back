<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use App\MenuRecipe;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Location;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // $userId = $request->input('user_id');
        $userId = 1;
        $year = date('Y');
        $month = date('m');

        $registeredMenus = Menu::leftJoin('menu_recipes', 'menus.id', '=', 'menu_recipes.menu_id')
        ->where('user_id', $userId)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->get();

        $registeredMenus = $this->getPhoto($registeredMenus, $userId);

        // JSON整形処理
        $returnMenus = array();
        for ($day = 1; $day <= date('t'); $day++) {
            // 日付
            array_push($returnMenus,
            array
            (
                "date" => $year . '-' . $month . '-' . sprintf('%02d', $day),
                "data" => []
            ));
            // カテゴリー
            for ($category = 0; $category <= 2; $category++) {
                array_push($returnMenus[$day-1]["data"],
                array
                (
                    "category" => $category,
                    "title" => "",
                    "data" => []
                ));
                // 出力位置
                for ($location = 0; $location <= 5; $location++) {
                    array_push($returnMenus[$day-1]["data"][$category]["data"],
                    array
                    (
                        "location" => $location,
                        "recipe_id" => 0,
                        "recipe_photo" => ""
                    ));
                }
        }
    }

    // 検索
    foreach ($registeredMenus As $registeredMenu)
    {
        // 更新する配列を取得
        // 日付による絞り込み
        $updateDateArray = array_filter($returnMenus, function($returnMenu) use($registeredMenu) {
            return $returnMenu["date"] === $registeredMenu['date'];
        });
        // カテゴリーによる絞り込み
        $updateCategoryArray = array_filter(reset($updateDateArray)["data"], function($updateDate) use($registeredMenu) {
            return $updateDate["category"] === $registeredMenu['category'];
        });
        // 出力位置による絞り込み
        $updateLocationArray = array_filter(reset($updateCategoryArray)["data"], function($updateCategory) use($registeredMenu) {
            return $updateCategory["location"] === $registeredMenu['location'];
        });
        // 登録済み献立情報で更新
        $dateIndex = key($updateDateArray);
        $categoryIndex = key($updateCategoryArray);
        $locationIndex = key($updateLocationArray);
        $returnMenus[$dateIndex]["data"][$categoryIndex]["title"] = $registeredMenu['title'];
        $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_id"] = $registeredMenu['recipe_id'];
        $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_photo"] = $registeredMenu['recipe_photo'];
    }

    return response()->json([
        'message' => 'OK!',
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

  public function getPhoto($menus, $userId)
  {
      $returnRecipes = array();
      $disk = Storage::disk('s3');
      foreach($menus as $value){
        $path = sprintf('%s%s%s%s%s%s', 'userId=', $userId, '/', 'recipeId=', $value->recipe_id, '/');
        Log::info($path);
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
            "id"=> $value->id,
            "user_id" => $value->user_id,
            "date" => $value->date,
            "category" => $value->category,
            "title"=> $value->title,
            "menu_id" => $value->menu_id,
            "location" => $value->location,
            "recipe_id" => $value->recipe_id,
            'recipe_photo' => $contents
        ));
     }
     return $returnRecipes;
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
