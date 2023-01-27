<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ReportChannel as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/report_channels')]
class ReportChannelsController extends BaseController
{
    protected static string $modelName = Model::class;
}
