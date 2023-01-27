<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $channel_id
 * @property int $merchant_id
 * @property string $order_number
 * @property string $trade_number
 * @property int $app_id
 * @property string $bank_code
 * @property string $bank_name
 * @property string $bank_branch
 * @property string $bank_card
 * @property string $name
 * @property float $amount
 * @property float $amount_paid
 * @property int $state
 * @property int $finished
 * @property int $created
 * @property int $updated
 * @property string $remark
 * @property string $ip
 * @property int $upstream_confirmed
 * @property int $downstream_confirmed
 * @property int $downstream_notified
 * @property int $downstream_notify_count
 * @property string platform_rate
 * @property string rate
 * @property string $type
 * @property int parent_id
 * @property string parent_rate
 * @property string path
 */
class PayoutRecord extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    /**
     * 订单状态
     */
    const STATUS_TYPES = [
        0 => '处理中', 
        1 => '已完成', 
        2 => '已取消', 
        3 => '已拒绝', 
        4 => '交易关闭',
    ];
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_CANCEL = 2;
    const STATUS_DENY = 3;
    const STATUS_CLOSE = 4;
    protected ?string $table = 'payouts';
    protected ?string $dateFormat = 'U';
}