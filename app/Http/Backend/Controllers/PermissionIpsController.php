<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\PermissionIp as Model;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/permission_ips')]
class PermissionIpsController extends BaseController
{
    protected static string $modelName = Model::class;

    protected function getQueryCond(RequestInterface $request): array
    {
        return ['%' => 'ip'];
    }
}
