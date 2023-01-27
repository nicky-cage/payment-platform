<?php

declare(strict_types=1);

namespace App\Http\Frontend\Controllers;

use App\Http\Common\FrontendController;
use App\Http\Common\BaseJsonTrait;
use App\Http\Common\BaseCommonTrait;

abstract class BaseController extends FrontendController
{
    /**
     * 应用名称
     * @var string
     */
    protected static string $appName = 'frontend';

    use BaseCommonTrait;
    use BaseJsonTrait;
}
