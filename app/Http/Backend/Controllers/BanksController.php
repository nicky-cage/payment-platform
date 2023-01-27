<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Bank as Model;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/banks')]
class BanksController extends BaseController
{

    protected static string $modelName = Model::class;

    protected static array $disabledOperations = ['delete'];
}
