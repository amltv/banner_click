<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.02.2018
 * Time: 17:33
 */

namespace App\Components;

use App\Models\BannerStatistic;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatisticHelper
{
    const GROUP_DAY = 'day';
    const GROUP_WEEK = 'week';
    const GROUP_MONTH = 'month';

    /**
     * @param int $banner_id
     * @param string $group StatisticHelper::GROUP_
     * @param Carbon $from
     * @param Carbon $to
     * @return Collection
     */
    public static function getBannerStatistic(int $banner_id, string $group, Carbon $from, Carbon $to) : Collection
    {
        /** @var Collection $statistic */
        $statistic = DB::table('banner_statistics')
            ->where('banner_id', '=', $banner_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date', 'asc')
            ->get(['all', 'unique', 'date']);

        //Online статистику берем из кеша
        if ($statistic->count() > 0) {
            $now = Carbon::now();

            $lastDate = Carbon::parse($statistic->last()->date);
            $lastDate->addHour();

            $bannerComponent = app()->make('banner');

            while ($lastDate->diffInHours($now, false) >= 0) {
                $time = $lastDate->getTimestamp();

                $statistic->push((object) [
                    'all' => $bannerComponent->getAll($banner_id, $time),
                    'unique' => $bannerComponent->getUnique($banner_id, $time),
                    'date' => date('Y-m-d H:00:00', $time)
                ]);

                $lastDate->addHour();
            }
        }

        //Групируем на PHP, так как данные храняться в разных БД
        $result = new Collection();
        foreach ($statistic as $item) {
            $key = Carbon::parse($item->date)->format(static::getGroupString($group));

            if (isset($result[$key])) {
                $result[$key]->all += $item->all;
                $result[$key]->unique += $item->unique;
            } else {
                $result[$key] = $item;
                unset($result[$key]->date);
            }
        }

        return $result;
    }

    /**
     * @param string $group
     * @return string
     * @throws \Exception
     */
    protected static function getGroupString(string $group) : string
    {
        switch ($group) {
            case StatisticHelper::GROUP_DAY:
                return 'Y-m-d';
            case StatisticHelper::GROUP_WEEK:
                return 'Y-W';
            case StatisticHelper::GROUP_MONTH:
                return 'Y-m';
            default:
                throw new \Exception('Wrong group type');
        }
    }
}
