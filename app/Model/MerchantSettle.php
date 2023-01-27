<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property string $merchant_name
 * @property float $amount
 * @property float $fee
 * @property float $amount_settled
 * @property int $bank_id
 * @property string $card_number
 * @property string $real_name
 * @property string $branch_name
 * @property string $phone
 * @property int $province_id
 * @property int $city_id
 * @property int $district_id
 * @property int $state
 * @property int $created
 * @property int $updated
 * @property int $finished
 * @property int $admin_id
 * @property string $admin_name
 */
class MerchantSettle extends Model
{
    protected ?string $table = 'merchant_settles';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    const STATUS_TYPE = [0 => '待付', 1 => '完成', 2 => '取消', 3 => '拒绝', 9 => '其他'];
}
