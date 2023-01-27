<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $eposit_start
 * @property string $eposit_end
 * @property float $eposit_min
 * @property float $eposit_max
 * @property string $ithdraw_start
 * @property string $ithdraw_end
 * @property float $ithdraw_min
 * @property float $ithdraw_max
 */
class ChannelConfig extends Model
{
    protected ?string $table = 'channel_configs';
    public bool $timestamps = false;
}
