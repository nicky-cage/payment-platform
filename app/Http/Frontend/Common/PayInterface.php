<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Model\Order;
use App\Model\MerchantApp;
use Hyperf\HttpServer\Contract\RequestInterface;

interface PayInterface
{
    // ---------------------------------------------------- 支付部分 ----------------------------------------------------- //
    /**
     * 获取下单的数据
     * @param array $params
     * @param array $out_params
     * @return array
     */
    public static function payReadyData(array $params, array &$outParams = []): array;

    /**
     * 得到返回数据的处理结果
     * @param array $data
     * @return array
     */
    public static function payResult(array $data): array;

    // ---------------------------------------------------- 支付 - 异步部分 ----------------------------------------------------- //
    /**
     * 验证异步回调签名
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool;

    /**
     * 得到异步通知相关订单号码
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string;

    /**
     * 提前返回失败通知
     * @param string $message
     * @param ReqeustInterface $request
     * @return mixed
     */
    public static function notifyFailure(string $message, RequestInterface $request = null);

    /**
     * 返回异步结果给第三方
     * @param array $params
     * @param Order $order
     * @param MerchantApp $app 需要用到此app的回调URL
     * @return string
     */
    public static function notifyResult(array $params, Order $order, MerchantApp $app): string;

    // ---------------------------------------------------- 订单查询 ----------------------------------------------------- //
    public static function orderQuery(string $tradeNumber): array;

    /**
     * 查询余额
     */
    public static function balance(array $params): array;

    /**
     * 提交出款
     */
    public static function withdraw(array $params): array;

    /**
     * 出款查询
     */
    public static function withdrawQuery(array $params): array;
}
