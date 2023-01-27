<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $external_id
 * @property string $name
 * @property string $code
 * @property string $app_id
 * @property string $app_key
 * @property string $app_secret
 * @property int $encrypt_type
 * @property string $url_order
 * @property string $url_callback
 * @property string $url_notify
 * @property string $remark
 * @property int $state
 * @property int $created
 * @property int $updated
 * @property string $alias_name
 */
class Channel extends Model
{
    protected ?string $table = 'channels';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    protected ?string $dateFormat = 'U';
    /**
     * 状态类型
     */
    const STATUS_TYPES = [0 => '停用', 1 => '启用'];
    /**
     * @return array
     */
    public static function getRelatedAll(): array
    {
        $result = [];
        $rows = self::query()->orderBy('created', 'DESC')->get();
        foreach ($rows as $r) {
            $result[$r->id] = $r;
        }
        return $result;
    }
}
