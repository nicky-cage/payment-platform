<?php

declare(strict_types=1);

namespace App\Model;

class ReportChannel extends Model
{
    protected ?string $table = 'report_channels';
    public bool $timestamps = false;
}
