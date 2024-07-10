<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CustomizeController extends Controller
{
    public function uploadCustomize(Request $request)
    {
        // 確認請求是否有檔案
        if ($request->hasFile('customize')) {
            $file = $request->file('customize');
            
            // 生成日期格式
            $date = now()->format('YmdHis');
            
            // 檔名
            $newFilename = $date . '.' . $file->getClientOriginalExtension();
            
            // 儲存檔案到指定目錄
            $path = $file->storeAs('public/customize', $newFilename);
            
            if ($path) {
                return response()->json([
                    'status' => 'success',
                    'data' => ['picture' => $newFilename],
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'data' => null,
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'data' => null,
            ], 400);
        }
    }
}
