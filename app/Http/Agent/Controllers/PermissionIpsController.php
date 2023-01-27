<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\PermissionIp as Model;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/permission_ips')]
class PermissionIpsController extends BaseController
{
    protected static string $modelName = Model::class;

    protected function getQueryCond(RequestInterface $request): array
    {
        return ['%' => 'ip'];
    }
}
