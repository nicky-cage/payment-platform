<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $password
 * @property int $role_id
 * @property string $role_name
 * @property int $state
 * @property int $created
 * @property int $updated
 * @property int $deleted
 * @property string $allow_ips
 * @property string $salt
 * @property int $login_count
 * @property int $last_login
 * @property string $last_ip
 * @property string $mail
 * @property string $nickname
 * @property string $current_ip
 * @property int $google_code
 * @property string $google_verify
 *
 */
class Admin extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    protected ?string $table = 'admins';
    protected ?string $dateFormat = 'U';
}
