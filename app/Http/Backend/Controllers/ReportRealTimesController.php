<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ReportRealTime as Model;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller(prefix: '/backend/report_real_times')]
class ReportRealTimesController extends BaseController
{
    protected static string $modelName = Model::class;

    protected function getQueryCond(RequestInterface $request): array
    {
        return [
            '%' => 'merchant_name'
        ];
    }
}
