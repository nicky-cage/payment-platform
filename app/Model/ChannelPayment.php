<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Db;

/**
 * @property  int $id
 * @property  int $channel_id
 * @property  string $name
 * @property  string $code
 * @property  float $amount_min
 * @property  float $amount_max
 * @property  string $amounts
 * @property  int $state
 * @property  int $created
 * @property  int $updated
 * @property int rate
 * @property  int $payment_type
 */
class ChannelPayment extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    /**
     * 状态类型
     */
    const STATUS_TYPES = [0 => '禁用', 1 => '正常'];
    /**
     * @var array
     */
    private static array $paymentTypes = ['rows' => [], 'cached' => 0];
    /**
     * 缓存时间
     * @var int
     */
    private static int $paymentTypeCacheTime = 3600;
    protected ?string $table = 'channel_payments';
    protected ?string $dateFormat = 'U';

    /**
     * @return array
     */
    public static function getPayments(): array
    {
        if (self::$paymentTypes['rows'] && self::$paymentTypes['cached'] > time() - self::$paymentTypeCacheTime) {
            // 如果在缓存时间之内
            return self::$paymentTypes['rows'];
        }
        $sql = 'SELECT c.id AS channel_id, c.name AS channel_name, ' .
            'p.code AS code, p.name AS name, p.amount_min, p.amount_max, p.amounts, p.payment_type, p.state ' .
            'FROM channels AS c INNER JOIN channel_payments as p ON c.id = p.channel_id ' .
            'WHERE c.state = 1 and p.state = 1 ';
        $result = [];
        $rows = Db::select($sql);
        foreach ($rows as $r) {
            $channelId = $r->channel_id;
            if ($r->amounts != '') {
                $r->amounts = explode(',', $r->amounts);
                if (count($r->amounts) >= 1) {
                    $r->amounts = array_map(function ($v) {
                        return round($v, 2);
                    }, $r->amounts);
                }
            } else {
                $r->amounts = [];
            }
            $r->amount_min = round($r->amount_min, 2);
            $r->amount_max = round($r->amount_max, 2);
            if (!isset($result[$channelId])) {
                $result[$channelId] = (object)['channel_id' => $channelId, 'channel_name' => $r->channel_name, 'pay_types' => [$r]];
                continue;
            }
            $result[$channelId]->pay_types[] = $r;
        }
        $rArr = array_values($result);
        self::$paymentTypes = ['rows' => $rArr, 'cached' => time()];
        return $rArr;
    }

    public static function getRelatedAll(): array
    {
        $rows = self::all();
        $rArr = [];
        foreach ($rows as $row) {
            $rArr[$row->id] = $row->name;
        }
        return $rArr;
    }
}
