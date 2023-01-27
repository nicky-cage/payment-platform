<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\ReportYear as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/report_years')]
class ReportYearsController extends BaseController
{
    protected static string $modelName = Model::class;
}
