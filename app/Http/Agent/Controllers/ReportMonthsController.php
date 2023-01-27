<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\ReportMonth as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/report_months')]
class ReportMonthsController extends BaseController
{
    protected static string $modelName = Model::class;
}
