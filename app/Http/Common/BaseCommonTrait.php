<?php

declare(strict_types=1);

namespace App\Http\Common;

use Illuminate\Support\Facades\DB;
use Hyperf\HttpServer\Contract\RequestInterface;

trait BaseCommonTrait
{
    /**
     * @param RequestInterface $request
     * @return bool
     */
    protected static function isAjax(RequestInterface $request): bool
    {
        $ajaxHeader = $request->header('X-Requested-With');
        return $ajaxHeader && strtolower($ajaxHeader) == 'xmlhttprequest';
    }

    /**
     * @return mixed
     */
    protected static function getSQL(): array
    {
        DB::connection()->enableQueryLog();
        return DB::getQueryLog();
    }
}
