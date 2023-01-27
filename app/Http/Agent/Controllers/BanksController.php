<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Bank as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/banks')]
class BanksController extends BaseController
{

    protected static string $modelName = Model::class;

    protected static array $disabledOperations = ['delete'];
}
