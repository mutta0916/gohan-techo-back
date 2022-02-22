<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use App\MenuRecipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $year = $request->input('year');
        $month = $request->input('month');

        $registeredMenus = Menu::leftJoin('menu_recipes', 'menus.id', '=', 'menu_recipes.menu_id')
        ->leftJoin('recipes', 'menu_recipes.recipe_id', '=', 'recipes.id')
        ->where('menus.user_id', $userId)
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
                "date" => $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day),
                "data" => []
            ));
            // カテゴリー
            for ($category = 0; $category <= 2; $category++) {
                array_push($returnMenus[$day-1]["data"],
                array
                (
                    "id" => 0,
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
                        "name" => "",
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
        $dateIndex = key($updateDateArray);
        // カテゴリーによる絞り込み
        $updateCategoryArray = array_filter(reset($updateDateArray)["data"], function($updateDate) use($registeredMenu) {
            return $updateDate["category"] === $registeredMenu['category'];
        });
        $categoryIndex = key($updateCategoryArray);
        $returnMenus[$dateIndex]["data"][$categoryIndex]["id"] = $registeredMenu['menu_id'];
        $returnMenus[$dateIndex]["data"][$categoryIndex]["title"] = $registeredMenu['title'];
        // 出力位置による絞り込み
        if ( $registeredMenu['location'] ){
            $updateLocationArray = array_filter(reset($updateCategoryArray)["data"], function($updateCategory) use($registeredMenu) {
                return $updateCategory["location"] === $registeredMenu['location'];
            });
            $locationIndex = key($updateLocationArray);
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_id"] = $registeredMenu['recipe_id'];
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["name"] = $registeredMenu['name'];
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_photo"] = $registeredMenu['recipe_photo'];
        }
    }
    Log::info($returnMenus);
    return response()->json([
        'message' => 'OK!',
        'menus' => $returnMenus
    ], 200);
  }

  public function store(Request $request)
  {
    // 献立テーブル更新
    $userId = $request->input('user_id');
    $menuId = $request->input('menu_id');
    $menu = Menu::updateOrCreate(
      ['id' => $menuId],
      ['user_id' => $userId, 'date' => $request->input('date'), 'category' => $request->input('category'), 'title' => $request->input('title') ]
    );

    if( !$request->input('title') ) {
        $menuId = $menu->id;
        // 献立明細テーブル更新
        MenuRecipe::updateOrCreate(
          ['menu_id' => $menuId, 'location' => $request->input('location')],
          ['recipe_id' => $request->input('recipe_id') ]
        );
    }

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
            "name" => $value->name,
            'recipe_photo' => $contents
        ));
     }
     return $returnRecipes;
  }

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
