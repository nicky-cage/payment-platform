<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

class AdminLogin extends Model
{
    const KEY = 'admininfo';

    /**
     * @var string
     */
    public string $name;
    /**
     * @var int
     */
    public int $login_count;
    /**
     * @var int
     */
    public int $last_login;
    /**
     * @var string
     */
    public string $last_ip;
    /**
     * @var string
     */
    public string $current_ip;
    /**
     * @Inject
     * @var Redis
     */
    #[Inject]
    protected Redis $redis;
    /**
     * @var SessionInterface
     */
    #[Inject]
    protected SessionInterface $session;
    /**
     * @var string
     */
    private static string $cacheKey = 'login:admin:';

    /**
     * @param int $adminId
     * @return string
     */
    public static function getCacheKey(int $adminId): string
    {
        return self::$cacheKey . ':' . $adminId;
    }
    /**
     * 保存到缓存
     * @param array $data
     */
    public function store(array $data)
    {
        $savedData = (object) [
            'id' => $data['id'],
            'name' => $data['name'],
            'login_count' => $data['login_count'],
            'last_login' => $data['last_login'],
            'last_ip' => $data['last_ip'],
            'current_ip' => $data['current_ip']
        ];
        $cacheKey = self::getCacheKey($data['id']);
        $this->redis->set($cacheKey, json_encode($savedData));
    }

    public static function set(SessionInterface $session, Admin $row)
    {
        $data = $row->toArray();
        $session->set(self::KEY, $data);
    }

    public static function get(SessionInterface $session): ?Admin
    {
        $data =  $session->get(self::KEY);
        if (!$data) {
            return null;
        }
        $object = new Admin();
        foreach ($data as $k => $v) {
            $object->$k = $v;
        }
        return $object;
    }
}
