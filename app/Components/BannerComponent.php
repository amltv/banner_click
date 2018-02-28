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
        $time = time();

        //Считаем все клики
        $keyAllClicks = $this->buildKeyString(BannerComponent::TYPE_ALL, $banner_id, $time);
        $pipeline = Redis::pipeline()->incr($keyAllClicks);
        $pipeline->expire($keyAllClicks, $this->config['expireTime']);

        //Считаем только уникальные клики
        //Считаем что повторный клик после 00-00 следующего дня является уникальным кликом
        $unique_key = $this->buildKeyStringForUser(BannerComponent::TYPE_UNIQUE, $banner_id, $time, $user_id);
        if (Redis::incr($unique_key) === 1) {//incr для исключения повторного засчета при одновременном нажатии на банер
            $pipeline->expire($unique_key, 86400);

            //Увеличиваем счетчик для уникальных ключей
            $keyUniqueClicks = $this->buildKeyString(BannerComponent::TYPE_UNIQUE, $banner_id, $time);
            $pipeline->incr($keyUniqueClicks);
            $pipeline->expire($keyUniqueClicks, $this->config['expireTime']);
        }

        $pipeline->execute();
    }

    /**
     * @param int $banner_id
     * @param int $timestamp
     * @return mixed
     */
    public function getAll(int $banner_id, int $timestamp) : int
    {
        return Redis::get($this->buildKeyString(BannerComponent::TYPE_ALL, $banner_id, $timestamp)) ?:0 ;
    }

    /**
     * @param int $banner_id
     * @param int $timestamp
     * @return mixed
     */
    public function getUnique(int $banner_id, int $timestamp) : int
    {
        return Redis::get($this->buildKeyString(BannerComponent::TYPE_UNIQUE, $banner_id, $timestamp)) ?: 0;
    }

    /**
     * Возвращает ключ для хранения кликов с групировкой по часам
     *
     * @param string $name
     * @param int $banner_id
     * @param int $timestamp
     * @return string
     */
    protected function buildKeyString(string $name, int $banner_id, int $timestamp) : string
    {
        return "$name:$banner_id:" . date('YmdH', $timestamp);
    }

    /**
     * Возвращает ключ для хранения клика пользователя
     * Ключ возвращается в формате Ymd что обеспечивает нам учет клика в интервале 00-00 до 00-00 следующего дня
     *
     * @param string $name
     * @param int $banner_id
     * @param int $timestamp
     * @param int $user_id
     * @return string
     */
    protected function buildKeyStringForUser(
        string $name,
        int $banner_id,
        int $timestamp,
        int $user_id
    ) : string
    {
        return "$name:$banner_id:$user_id:" . date('Ymd', $timestamp);
    }
}
