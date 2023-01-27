<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $year
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
class ReportYear extends Model
{
    protected ?string $table = 'report_years';
    public bool $timestamps = false;
}
