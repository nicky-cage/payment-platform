<?php

declare(strict_types=1);

namespace App\Common;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Redis\Redis;
use App\Model\Model;

class Cache
{
    /**
     * @var Redis
     */
    private static Redis $cache;

    /**SSSS
     * 返回redis客户端, 为了方便使用编辑器的代码提示,不再继续简化aaaa
     * @return Redis
     */
    public static function get(): Redis
    {
        if (!self::$cache) {
            $container = ApplicationContext::getContainer();
            self::$cache = $container->get(Redis::class);
        }
        return self::$cache;
    }

    /**
     * 得到类缓存
     * @param string $modelName
     * @param int $id
     * @param int $timeout
     * @return Model|NULL
     */
    public static function getModelById(string $modelName, int $id, int $timeout = 86400): Model
    {
        $key = last(explode('\\', $modelName)) . '_' . $id;
        $cache = self::get();
        $value = $cache->get($key);
        $now = time();
        if ($value) {
            $row = unserialize($value);
            if ($row->cached_time_by_redis > $now - $timeout) {
                return $row;
            }
        }

        $record = $modelName
            ::query()
            ->where('id', $id)
            ->select();
        $row = $record->toArray();
        $row->cached_time_by_redis = $now;
        $cache->set($key, serialize($row));
        return $row;
    }
}
