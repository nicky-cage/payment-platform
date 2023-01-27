<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\ReportChannel as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/report_channels')]
class ReportChannelsController extends BaseController
{
    protected static string $modelName = Model::class;
}
