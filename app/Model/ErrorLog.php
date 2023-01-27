<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $error
 * @property string $data
 * @property int $error_type
 * @property int $created
 * @property string $ip
 * @property string $url
 */
class ErrorLog extends Model
{
    protected ?string $table = 'error_logs';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = null;
}
