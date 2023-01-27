<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property int $card_id
 * @property int $state
 * @property int $polling
 */
class MerchantCard extends Model
{
    protected ?string $table = 'merchant_cards';
    public bool $timestamps = false;
    const STATUS_TYPES = [0 => '停用', 1 => '启用'];
    const POLLING_TYPES = [0 => '否', 1 => '是'];
}
