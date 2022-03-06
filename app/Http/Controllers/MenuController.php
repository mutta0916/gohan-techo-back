<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use App\MenuRecipe;
use Illuminate\Support\Facades\Storage;

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
        ->get(['menus.id', 'menus.user_id', 'date', 'category', 'title', 'menu_recipes.id as menu_recipes_id', 'location', 'recipe_id', 'name']);

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
                        "menu_recipes_id" => 0,
                        "location" => $location,
                        "recipe_id" => 0,
                        "recipe_name" => "",
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
            return $returnMenu["date"] === $registeredMenu["date"];
        });
        $dateIndex = key($updateDateArray);
        // カテゴリーによる絞り込み
        $updateCategoryArray = array_filter(reset($updateDateArray)["data"], function($updateDate) use($registeredMenu) {
            return $updateDate["category"] === $registeredMenu["category"];
        });
        $categoryIndex = key($updateCategoryArray);
        $returnMenus[$dateIndex]["data"][$categoryIndex]["id"] = $registeredMenu["id"];
        $returnMenus[$dateIndex]["data"][$categoryIndex]["title"] = $registeredMenu["title"];
        // 出力位置による絞り込み
        if ( !is_null($registeredMenu['location']) ){
            $updateLocationArray = array_filter(reset($updateCategoryArray)["data"], function($updateCategory) use($registeredMenu) {
                return $updateCategory["location"] === $registeredMenu["location"];
            });
            $locationIndex = key($updateLocationArray);
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["menu_recipes_id"] = $registeredMenu["menu_recipes_id"];
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_id"] = $registeredMenu["recipe_id"];
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_name"] = $registeredMenu["name"];
            $returnMenus[$dateIndex]["data"][$categoryIndex]["data"][$locationIndex]["recipe_photo"] = $registeredMenu["recipe_photo"];
        }
    }
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

    if( $request->input('update_target') === 1 ) {
        // 献立明細テーブル更新
        MenuRecipe::updateOrCreate(
          ['id' => $request->input('menu_recipes_id')],
          ['menu_id' => $menu->id, 'location' => $request->input('location'), 'recipe_id' => $request->input('recipe_id') ]
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
            "menu_recipes_id" => $value->menu_recipes_id,
            "location" => $value->location,
            "recipe_id" => $value->recipe_id,
            "name" => $value->name,
            'recipe_photo' => $contents
        ));
     }
     return $returnRecipes;
  }

  public function destroy($menuRecipesId)
  {
      // 献立明細テーブル更新
      MenuRecipe::find($menuRecipesId)->delete();

      return response()->json([
        'message' => 'MenuRecipes deleted successfully'
    ], 204);
  }
}
