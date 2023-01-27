<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ReportYear as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/report_years')]
class ReportYearsController extends BaseController
{
    protected static string $modelName = Model::class;
}
