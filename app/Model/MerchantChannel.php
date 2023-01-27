<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $merchant_id
 * @property string $merchant_name
 * @property int $channel_id
 */
class MerchantChannel extends Model
{
    protected ?string $table = 'merchant_channels';
    public bool $timestamps = false;
}
