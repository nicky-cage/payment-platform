<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $day
 * @property int $merchant_id
 * @property string $merchant_name
 * @property float $income
 * @property float $cost
 * @property float $profit
 * @property float $success_total
 * @property int $success_count
 * @property float $failure_total
 * @property int $failure_count
 */
class ReportDay extends Model
{
    public bool $timestamps = false;
    protected ?string $table = 'report_days';
}