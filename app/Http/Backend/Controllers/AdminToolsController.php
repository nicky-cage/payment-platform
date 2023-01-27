<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\{Controller, RequestMapping};

#[Controller(prefix: '/backend/admin_tools')]
class AdminToolsController extends BaseController
{
    #[RequestMapping(path: 'clear', methods: 'post, get')]
    public function clear(RequestInterface $request, RenderInterface $render): ResponseInterface {
        return self::render($request, $render, ['action' => 'clear'], 'admin_tools/clear');
    }
}
