<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\MerchantChannel as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/merchant_channels')]
class MerchantChannelsController extends BaseController
{
    protected static string $modelName = Model::class;
}
