<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Http\Common\BackendController;
use App\Http\Middlewares\CheckMerchantLogin;
use Hyperf\HttpServer\Annotation\Middleware;

#[Middleware(CheckMerchantLogin::class)]
class BaseController extends BackendController
{
    protected static string $appName = 'agent';
}
