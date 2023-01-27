<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ErrorLog as Model;
use App\Model\Bank;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/error_logs')]
class ErrorLogsController extends BaseController
{
    protected static string $modelName = Model::class;

    protected function getQueryCond(RequestInterface $request): array
    {
        return [
            '%' => 'url, ip',
        ];
    }
}
