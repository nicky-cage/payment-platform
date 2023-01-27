<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\ChannelConfig as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/channel_configs')]
class ChannelConfigsController extends BaseController
{
    protected static string $modelName = Model::class;
}
