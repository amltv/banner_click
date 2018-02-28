<?php

namespace App\Http\Controllers;

use App\Components\StatisticHelper;
use App\Models\BannerStatistic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function show(Request $request, int $banner_id, string $group)
    {
        $from = Carbon::parse($request->get('from') ?: '2000-01-01');
        $to = Carbon::parse($request->get('to') ?: date('Y-m-d H:i:s'));

        $statistic = StatisticHelper::getBannerStatistic($banner_id, $group, $from, $to);

        return response()->json($statistic);
    }
}
