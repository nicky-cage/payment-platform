<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $day
 * @property int $merchant_id
 * @property string $merchant_name
 * @property float $pay_money
 * @property float $pay_cost
 * @property int $pay_num
 * @property float $withdraw_money
 * @property int $withdraw_num
 * @property float $withdraw_cost
 * @property int $agent_money
 */
class AgentDayReport extends Model
{
    public bool $timestamps = false;
    protected ?string $table = 'agent_day_report';
}