<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 商户登录后台：https://mch.pppay.cc/start/index.html
 * 商户名称：dd77
 * 商户ID：1099
 * 账号：dd77
 * 密码：aa123123
 * 支付密码：aa123123
 * 商户API密钥：2ARDIKXPBTENIZHWGXIOCYI19F5L8QYLZYEJA1HKZUUBRPMBAB4OSBODJTHVUWT26ZB14U1LXGP0WIDGIZMDB1XHKIROIYUHLNSPCAX8WZXFLM7AVHBMQKOLRFQVWGLW
 * 对接api文档：
 * 下单网关查看文档：
 * https://doc.pppay.cc/pay_api.pdf
 */
class HuangJiaPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'PHJ';
    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '1099';
    /**
     * 商户密钥
     * @var string
     */
    public static string $key = '2ARDIKXPBTENIZHWGXIOCYI19F5L8QYLZYEJA1HKZUUBRPMBAB4OSBODJTHVUWT26ZB14U1LXGP0WIDGIZMDB1XHKIROIYUHLNSPCAX8WZXFLM7AVHBMQKOLRFQVWGLW';
    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'https://apin7oqm6.zpqvj.xyz';
    private static string $appId = '21803dabe34e4227af1ee5ff928ac09a';

    /**
     * 得到处理过之后的数据
     * @param array $params
     * @return array|mixed
     */
    public static function payReadyData(array $params, array &$outParams = []): array
    {
        // -- 客户端提交信息 --
        $notifyURL = self::getNotifyURL();
        $data = [
            'mchId' => self::$merchantId,
            'appId' => self::$appId,
            'productId' => $params['type'],
            'mchOrderNo' => $params['trade_number'],
            'amount' => floatval($params['amount']) * 100, // 以分为单位
            'notifyUrl' => $notifyURL,
        ];
        $data['sign'] = self::getSign($data);
        print_r($data);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = 'https://pay.pppay.cc/api/pay/create_order'; // 下单地址
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        echo "[皇家]支付结果: ", $content, "\n";
        $result = json_decode($content);
        if (!isset($result->retCode) || !isset($result->payOrderId) || !isset($result->payJumpUrl)) {
            return self::jsonErr($result->retMsg ?? '支付结果有误');
        }
        if ($result->retCode != 0) {
            return self::jsonErr($result->regMsg?? '发起支付有误');
        }
        return self::jsonResult(['url' => $result->payJumpUrl]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['mchOrderNo'] ?? '';
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool
    {
        $sign = self::getSign($params);
        return $params['sign'] == $sign;
    }

    /**
     * 返回异步结果给第三方
     * @param array $params
     * @param Order $order
     * @param MerchantApp $app
     * @return string
     */
    public static function notifyResult(array $params, Order $order, MerchantApp $app): string
    {
        $amount = isset($params['amount']) && is_numeric($params['amount']) ? intval($params['amount']) / 100 : 0;
        $status = isset($params['status']) && in_array($params['status'], [-2, 0, 1, 2, 3, 4]) ? intval($params['status']) : 0;
        $success = $amount == $order->amount && ($status == 2 || $status == 3);

        $currentTime = time();
        $order->state = ($success ? 1 : 0);
        $order->finished = $currentTime;
        $order->amount_paid = ($success ? $amount : 0);
        $order->updated = $currentTime;
        $order->upstream_confirmed = $currentTime; // 上游确认时间
        if ($order->save()) { // 保存订单状态
            return 'success';
        }
        return 'Error';
    }

    /**
     * @param string $tradeNumber
     * @param Order|null $order
     * @return array
     * @throws GuzzleException
     */
    public static function orderQuery(string $tradeNumber, Order $order = null): array
    {
        $url = 'https://pay.pppay.cc/api/pay/query_order'; // 查询订单
        $data = [
            'mchId' => self::$merchantId,
            'mchOrderNo' => $tradeNumber,
            'reqTime' => date('YmdHis'),
            'version' => '1.0',
        ];
        $data['sign'] = self::getSign($data);
        print_r(['query' => $data]);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        print_r($result);

        return [
            'orderNum' => $tradeNumber,
            'success' => isset($result->mchId) && $result->mchId == self::$merchantId
                && isset($result->amount) && intval($result->amount) / 100 == $order->amount
                && isset($result->status) && ($result->status == 2 || $result->status == 3),
        ];
    }

    /**
     * @param array $data
     * @return string
     */
    private static function getSign(array $data): string
    {
        ksort($data);
        $signStr = '';
        foreach ($data as $k => $v) {
            if ($k == 'sign' || trim(strval($v)) == '') {
                continue;
            }
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr .= 'key=' . self::$key;
        return strtoupper(md5($signStr));
    }
}
