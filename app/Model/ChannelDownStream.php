<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property  int $id
 * @property  int $up_stream_id
 * @property  string $up_stream_name
 * @property  float $fee
 * @property  float $fee_min
 * @property   string $code
 * @property  int $created
 * @property  int $updated
 */
class ChannelDownStream extends Model
{
    protected ?string $table = 'channel_down_streams';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
