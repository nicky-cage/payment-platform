<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;
use App\Common\HttpClient;
use Hyperf\Utils\Str;

/**
 * 商户登录：https://animateseo990.xyz/web990/signin
 * 账号：youyoumember
 * 密码：UXpWCzGbmd7gz5Cy
 * appid：youyou
 * merchantkey：v3uzNwpjCmvfRUSbfTcT4R8cJY6ZwFwvbTJyqzS7JvRQZH2TsHBeUJjuyA74
 * 回调IP：47.243.56.167
 * API文档：https://animateseo990.xyz/wiki.html
 */
class TianJiBaXingPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TBX';

    // /**
    //  * 错误信息
    //  * @var string[]
    //  */
    // private static array $errArr = [
    //     '0' => '处理成功',
    //     '-1' => '系统繁忙',
    //     '40001' => '请求参数有误(缺少或者格式有误)',
    //     '40002' => '尚未登录/回话超时',
    //     '40500' => '处理扫码付错误',
    //     '40510' => '不支持的通道',
    //     '40511' => '通道已失效',
    //     '40512' => '请求IP不合法',
    //     '40513' => '商户不合法',
    //     '40520' => '订单不存在',
    //     '40521' => '订单已经存在',
    //     '40530' => '余额不足',
    //     '40531' => '提现时间段不支持',
    //     '40532' => '提现金额错误',
    //     '50001' => '未授权',
    //     '50002' => '签名错误',
    // ];

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = 'youyou';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'v3uzNwpjCmvfRUSbfTcT4R8cJY6ZwFwvbTJyqzS7JvRQZH2TsHBeUJjuyA74';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'https://animateseo990.xyz';

    /**
     * @param array $data
     * @return string
     */
    private static function getSign(array $data): string
    {
        ksort($data);
        $originStr = '';
        foreach ($data as $k => $v) {
            if ($k == 'sign' || !$v) {
                continue;
            }
            $originStr .= "${k}=${v}&";
        }
        $originStr .= 'key=' . self::$key;
        return strtoupper(md5($originStr));
    }

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        $data = [
            'appId' => self::$merchantId,
            'version' => '1.0',
            'nonceStr' => Str::random(16),
            'orderId' => $params['trade_number'],
            'amount' => intval($amount * 100),
            'payChannel' => $type, // 通道代码
            'goodsName' => $params['name'], // 商品名称
            'goodsDesc' => '天际' . time(),
            'clientIp' => $params['client_ip'],
            'asyncNotifyUrl' => self::getNotifyURL(),
            'tradeType' => 'WEB',
        ];
        $data['sign'] = self::getSign($data);
        $outParams = ['data' => $data];
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $client = HttpClient::getClient();
        $url = self::$urlSubmit . '/proxy/pay/unifiedorder';
        $content = $client->post($url, $data);
        $result = json_decode($content);
        if (isset($result->data)) {
            return self::jsonResult(['url' => $result->data]);
        }
        return self::jsonErr('提交三方支付错误');
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['orderId'] ?? '';
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool
    {
        if (!isset($params['sign'])) {
            return false;
        }
        $signFrom = trim($params['sign']);
        $sign = self::getSign($params);
        return $signFrom == $sign;
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
        if (!isset($params['amount']) || !is_numeric($params['amount']) || $params['amount'] <= 0) {
            return 'Error: 金额字段有误';
        }

        $amount = intval($params['amount']) / 100; // 金额 = 分, 要除以100
        if ($order->amount != $amount) { // 金额对比
            return 'Error: 处理状态[成功]失败, 请再次提交';
        }

        $currentTime = time();
        $order->state = 1;
        $order->finished = $currentTime;
        $order->amount_paid = $amount;
        $order->updated = $currentTime;
        $order->upstream_confirmed = $currentTime; // 上游确认时间
        if ($order->save()) { // 保存订单状态
            return 'success';
        }
        return 'Error: 处理状态[成功]失败, 请再次提交';
    }

    /**
     * @param string $tradeNumber
     * @param Order|null $order
     * @return array
     * @throws GuzzleException
     */
    public static function orderQuery(string $tradeNumber, Order $order = null): array
    {
        $url = self::$urlSubmit . '/proxy/pay/order/query';
        $data = [
            'appId' => self::$merchantId,
            'version' => '1.0',
            'nonceStr' => Str::random(16),
            'orderId' => $tradeNumber,
        ];
        $data['sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);

        if (!isset($result->orderId) || !isset($result->pay)) {
            return [
                'orderNum' => '',
                'success' => false,
            ];
        }

        return [
            'orderNum' => $tradeNumber,
            'success' => $result->pay, // 订单是否成功 1-未支付 2-成功单
        ];
    }
}
