<?php

declare(strict_types=1);

namespace App\Http\Common;

abstract class FrontendController extends BaseController
{

    /**
     * 应用名称
     * @var string
     */
    protected static string $appName = 'frontend';

    use BaseCommonTrait;
    use BaseJsonTrait;
}
