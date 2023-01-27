<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property int $app_id
 * @property string $trade_number
 * @property int $channel_id
 * @property string $request_url
 * @property string $request_ip
 * @property string $reply
 * @property int $created
 * @property string $remark
 * @property string $request_data
 */
class NotifyUp extends Model
{
    protected ?string $table = 'notify_ups';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
}
