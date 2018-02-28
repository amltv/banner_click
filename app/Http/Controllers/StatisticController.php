<?php

namespace App\Http\Controllers;

use App\Models\BannerStatistic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function show(Request $request, $banner_id)
    {
        /** @var Collection $statistic */
        $statistic = BannerStatistic::where('banner_id', '=', $banner_id)
            ->orderBy('date', 'desc')
            ->get(['all', 'unique', 'date']);

        $bannerComponent = app()->make('banner');

        //FIXME скрипт на запись в БД запускается каждые 5 минут, так что в течении нескольких минут даннык на графики за предыдущий час могут не вывестись!!!

        $time = time();
        //За текущий час берем статистику из кеша
        $statistic->add([
            'all' => $bannerComponent->getAll($banner_id, $time) ?: 0,
            'unique' => $bannerComponent->getUnique($banner_id, $time) ?: 0,
            'date' => date('Y-m-d H:00:00', $time)
        ]);

        return response()->json($statistic);
    }
}
