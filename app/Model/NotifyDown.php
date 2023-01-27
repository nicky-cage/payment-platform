<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property int $app_id
 * @property string $order_number
 * @property string $notify_url
 * @property string $notify_reply
 * @property int $notify_status
 * @property int $created
 * @property int $failure_count
 * @property string $remark
 * @property int $channel_id
 * @property string $trade_number
 */
class NotifyDown extends Model
{
    protected ?string $table = 'notify_downs';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    public const STATUS_TYPES = [0 => '失败', 1 => '成功'];
}
