<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    public function click(Request $request, $banner_id)
    {
        //Проверяем что банер существует
        $banner = Cache::remember("models.banner.$banner_id", 1440, function () use ($banner_id) {
            return Banner::findOrFail($banner_id);
        });

        //$user_id = Auth::id(); Для упрощения задания user_id генерируется
        $user_id = rand(1, 999);

        //Засчитываем клик
        $bannerComponent = app()->make('banner');
        $bannerComponent->click($banner_id, $user_id);

        return response()->redirectTo($banner->url);
    }
}
