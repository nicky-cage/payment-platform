<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $admin_id
 * @property strign $remark
 * @property strign $method
 * @property strign $url
 * @property strign $operate_ip
 * @property strign $operate_area
 * @property int $created
 */
class OperationLog extends Model
{
    protected ?string $table = 'operation_logs';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
}
