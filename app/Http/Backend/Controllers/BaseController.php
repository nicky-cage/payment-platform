<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Http\Common\BackendController;
use App\Http\Middlewares\CheckAdminLogin;
use Hyperf\HttpServer\Annotation\Middleware;

#[Middleware(CheckAdminLogin::class)]
class BaseController extends BackendController
{
    protected static string $appName = 'backend';
}
