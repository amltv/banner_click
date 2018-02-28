<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\BannerStatistic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SaveToDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banner:save_to_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allBanners = Banner::all();

        foreach ($allBanners as $banner) {
            try {
                $this->save($banner);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }

    protected function save(Banner $banner)
    {
        $bannerComponent = app()->make('banner');

        $lastBannerStatistic = BannerStatistic::where('banner_id', '=', $banner->id)
            ->orderBy('date', 'desc')
            ->first();

        //Если нет статистики, то начинаем ее сохранять с текущего часа
        if (!$lastBannerStatistic) {
            $time = time();

            $newBannerStatistic = new BannerStatistic([
                'all' => $bannerComponent->getAll($banner->id, $time),
                'unique' => $bannerComponent->getUnique($banner->id, $time),
                'date' => date('Y-m-d H:00:00', $time)
            ]);
            $newBannerStatistic->banner()->associate($banner);

            $newBannerStatistic->saveOrFail();
            return;
        }

        $nowDate = Carbon::now();
        $lastDate = Carbon::parse($lastBannerStatistic->date);

        while ($lastDate->diffInHours($nowDate) > 0) {
            $lastDate->addHour();
            $time = $lastDate->getTimestamp();

            $newBannerStatistic = new BannerStatistic([
                'all' => $bannerComponent->getAll($banner->id, $time),
                'unique' => $bannerComponent->getUnique($banner->id, $time),
                'date' => date('Y-m-d H:00:00', $time)
            ]);
            $newBannerStatistic->banner()->associate($banner);

            $newBannerStatistic->saveOrFail();
        }
    }
}
