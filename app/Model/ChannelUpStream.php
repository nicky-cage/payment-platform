<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $priority
 * @property stirng $callback_ip
 * @property int $state
 * @property int $created
 * @property int $updated
 */
class ChannelUpStream extends Model
{
    protected ?string $table = 'channel_up_streams';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
