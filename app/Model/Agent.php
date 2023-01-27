<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $salt
 * @property int $parent_agent_id
 * @property string $parent_agent_name
 * @property string $parent_path
 * @property int $state
 * @property int $created
 * @property int $updated
 * @property string $allow_ip
 * @property int $login_count
 * @property int $last_login
 * @property string $last_ip
 * @property string $mail
 * @property string $phone
 */
class Agent extends Model
{
    protected ?string $table = 'agents';
    protected ?string $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
