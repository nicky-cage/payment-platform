<?php

declare(strict_types=1);

namespace App\Http\Frontend\Controllers;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\{Controller, GetMapping};

#[Controller(prefix: "/index")]
class IndexController extends BaseController
{
    /**
     * @return void
     */
    #[GetMapping(path: "test")]
    public function test(RequestInterface $request): array
    {
        return self::jsonOk();
    }
}
