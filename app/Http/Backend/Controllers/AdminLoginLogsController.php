<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\AdminLoginLog as ThisModel;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/admin_login_logs')]
class AdminLoginLogsController extends BaseController
{
    /**
     * @var string
     */
    protected static string $modelName = ThisModel::class;
}
