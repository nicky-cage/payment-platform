<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $order_number
 * @property string $channel_name
 * @property int $merchant_id
 * @property string $channel_code
 * @property string $card_id
 * @property float $amount
 * @property string $payer_name
 * @property string $payer_remark
 * @property string $bank_order_number
 * @property float $paid_amount
 * @property int $state
 * @property string $remark
 * @property int $created
 * @property int $updated
 * @property int $finished
 */
class CardRecord extends Model
{
    protected ?string $table = 'card_records';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    /**
     * 状态类型
     */
    const STATUS_TYPES = [0 => '待付', 1 => '实付', 2 => '取消', 3 => '其他'];
}
