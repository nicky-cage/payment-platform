<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\ChannelConfig as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/channel_configs')]
class ChannelConfigsController extends BaseController
{
    protected static string $modelName = Model::class;
}
