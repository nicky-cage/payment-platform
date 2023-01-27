<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property string $order_number
 * @property int $channel_id
 * @property float $amount
 * @property float $amount_paid
 * @property int $state
 * @property float $cost_ms
 * @property int $terminal_type
 * @property string $ip
 * @property string $area
 * @property string $trade_number
 * @property string $remark
 * @property int $created
 * @property int $updated
 * @property int $finished
 * @property int $app_id
 * @property int $upstream_confirmed
 * @property int $downstream_confirmed
 * @property int $downstream_notified
 * @property int $downstream_notify_count
 * @property string $return_url
 * @property string $pay_data
 * @property string $submit_data
 * @property string platform_rate
 * @property string rate
 * @property string $type
 * @property int parent_id
 * @property string parent_rate
 * @property string path
 * @property string $notify_url
 */
class Order extends Model
{

    const STATUS_PENDING = 0;   // 待支付
    const STATUS_FINISHED = 1;  // 已完成
    const STATUS_FAILURE = 2;  // 失败
    const STATUS_CANCELED = 3;   // 已删除
    const STATUS_DENY = 4;     // 交易关闭
    const STATUS_OTHER = 9;     // 交易关闭

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    /**
     * 订单状态
     */
    const STATUS_TYPES = [
        0 => '待支付',  //
        1 => '已完成',  // 成功
        2 => '失败',    //
        3 => '已取消',  //
        4 => '已拒绝',    //
        9 => '其他状态', //
    ];
    protected ?string $table = 'orders';
    protected ?string $dateFormat = 'U';

    // 是否成功
    public function isFinished(): bool
    {
        return $this->state == self::STATUS_FINISHED;
    }
}
