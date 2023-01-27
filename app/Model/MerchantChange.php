<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property string $merchant_name
 * @property int $change_type
 * @property float $remain_before
 * @property float $frozen_before
 * @property float $total_before
 * @property float $remain_after
 * @property float $frozen_after
 * @property float $total_after
 * @property float $amount
 * @property string $remark
 * @property int $created
 */
class MerchantChange extends Model
{
    protected ?string $table = 'merchant_changes';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const CHANGE_TYPES = [0 => '平账', 1 => '入账', 2 => '出账'];
}
