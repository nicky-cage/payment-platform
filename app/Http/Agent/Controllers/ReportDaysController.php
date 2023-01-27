<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\AgentDayReport as thisModel;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/report_days')]
class ReportDaysController extends BaseController
{
    protected static string $modelName = thisModel::class;
}