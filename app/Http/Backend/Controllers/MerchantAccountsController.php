<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\MerchantAccount as ThisModel;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: "/backend/merchant_accounts")]
class MerchantAccountsController extends BaseController
{
    protected static string $modelName = ThisModel::class;
}
