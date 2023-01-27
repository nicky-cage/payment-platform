<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ReportMonth as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/report_months')]
class ReportMonthsController extends BaseController
{
    protected static string $modelName = Model::class;
}
