<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ReportDay as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/report_days')]
class ReportDaysController extends BaseController
{
    protected static string $modelName = Model::class;
}
