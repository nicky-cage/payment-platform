<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $permission_type
 * @property string $ip
 * @property string $remark
 * @property int $state
 * @property int $created
 * @property int $updated
 */
class PermissionIp extends Model
{
    protected ?string $table = 'permission_ips';
    public bool $timestamps = false;
}
