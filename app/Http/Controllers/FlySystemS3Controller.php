<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FlySystemS3Controller extends Controller
{
  public function index()
  {
      Log::info('S3に画像保存その２');
      $disk = Storage::disk('s3');

      Log::info(getcwd());
      Log::info(__DIR__);
      $img_path = sprintf('%s%s', __DIR__, '/test.jpg');
      Log::info($img_path);
      $contents = file_get_contents($img_path);

      // S3 にファイルをアップロード（パスはバケットディレクトリを起点として相対パスで記述）
      $disk->put('test.jpg', $contents, 'public'); // ← (4)

      // S3の完全URLを得る
      $url = $disk->url('test/test.jpg'); // ← (5)
      // $url → https://s3-REGION.amazonaws.com/BUCKET_NAME/test/test.jpg みたいな完全URLが返る

      // S3上に指定ファイルが存在するか確認
      if($disk->exists('test.jpg')) { // ← (6)
          echo'<pre>'; echo 'exists'; echo'</pre>';
      } else {
          echo'<pre>'; echo 'NONE'; echo'</pre>';
      }

      // ファイル取得はget()
      $contents = $disk->get('test.jpg'); // ← (7)

      // サーバに保存（ダウンロード）
      $put_path = sprintf('%s%s', __DIR__, '/test_put.jpg');
      file_put_contents($put_path, $contents);

  }
}
