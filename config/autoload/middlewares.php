<?php
declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Http\Middlewares\{CheckAdminLogin, CheckMerchantLogin};
use Hyperf\Session\Middleware\SessionMiddleware;

return [
    'http' => [
        SessionMiddleware::class,
        CheckMerchantLogin::class,
        CheckAdminLogin::class,
    ],
];
