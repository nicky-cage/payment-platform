<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $admin_id
 * @property string $domain
 * @property  string $login_ip
 * @property string $login_area
 * @property int $created
 * @property string user_agent
 */
class AdminLoginLog extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = null; // = 'created';

    protected ?string $table = 'admin_login_logs';
    protected ?string $dateFormat = 'U';
}
