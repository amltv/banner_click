<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.02.2018
 * Time: 10:18
 */

namespace App\Components;

use Illuminate\Support\Facades\Redis;

class BannerComponent
{
    const TYPE_ALL = 'all';
    const TYPE_UNIQUE = 'unique';

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param int $banner_id
     * @param int $user_id
     */
    public function click(int $banner_id, int $user_id)
    {
        //TODO Добавить устаревание ключей

        //Считаем все клики
        $pipeline = Redis::pipeline()->incr($this->buildKeyString(BannerComponent::TYPE_ALL, $banner_id, time()));;

        //Считаем только уникальные клики
        $unique_key = $this->buildKeyString(BannerComponent::TYPE_UNIQUE, $banner_id, time(), $user_id);
        if (Redis::incr($unique_key) === 1) {//incr для исключения повторного засчета при одновременном нажатии на банер
            //Считаем что уникальный клик действителен в течении 24 часов с момента предыдущего клика
            $pipeline->expire($unique_key, 86400);
            $pipeline->incr($this->buildKeyString(BannerComponent::TYPE_UNIQUE, $banner_id, time()));
        }

        $pipeline->execute();
    }

    /**
     * @param int $banner_id
     * @param int $timestamp
     * @return mixed
     */
    public function getAll(int $banner_id, int $timestamp)
    {
        return Redis::get($this->buildKeyString(BannerComponent::TYPE_ALL, $banner_id, $timestamp));
    }

    /**
     * @param int $banner_id
     * @param int $timestamp
     * @return mixed
     */
    public function getUnique(int $banner_id, int $timestamp)
    {
        return Redis::get($this->buildKeyString(BannerComponent::TYPE_UNIQUE, $banner_id, $timestamp));
    }

    /**
     * @param string $name
     * @param int $banner_id
     * @param int $timestamp
     * @param int|null $user_id
     * @return string
     */
    protected function buildKeyString(string $name, int $banner_id, int $timestamp, int $user_id = null) : string
    {
        return "$name:$banner_id:$user_id:" . date('YmdH', $timestamp);
    }
}
