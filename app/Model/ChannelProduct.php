<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $state
 * @property string $remark
 * @property int $created
 * @property int $updated
 */
class ChannelProduct extends Model
{
    protected ?string $table = 'channel_products';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    const STATUS_TYPES = [0 => '禁用', 1 => '正常'];
    /**
     * @param array $data
     * @return bool
     */
    public static function beforeSave(array &$data): bool
    {
        $now = time();
        $data['created'] = $now;
        $data['updated'] = $now;
        return true;
    }
}
