<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property string $merchant_name
 * @property float $remain
 * @property float $frozen
 * @property float $total
 * @property float $total_in
 * @property float $total_out
 * @property int $state
 * @property int $created
 * @property int $updated
 * @property int app_count
 * @property int order_count
 * @property int order_valid
 * @property int order_success
 * @property float order_total
 * @property float order_total_valid
 * @property float total_withdraw
 */
class MerchantAccount extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    const STATUS_TYPES = [0 => '禁用', 1 => '正常'];
    protected ?string $table = 'merchant_accounts';
    protected ?string $dateFormat = 'U';
}
