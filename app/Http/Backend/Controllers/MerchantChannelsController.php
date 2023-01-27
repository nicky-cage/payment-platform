<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\MerchantChannel as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/merchant_channels')]
class MerchantChannelsController extends BaseController
{
    protected static string $modelName = Model::class;
}
