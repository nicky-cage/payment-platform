<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use GuzzleHttp\Exception\GuzzleException;
use App\Model\Order;
use App\Model\MerchantAPP;
use GuzzleHttp\Client;

/**
 * 登入网址：
 * https://mer.lcilf.cn/#/login?redirect=%2F
 *
 * 商户号：110507184303
 * 帐号：TJBET
 * 密码：Aa123456
 * 登录密码: qwe123QWE!@#
 * 交易密码: ASDFG!@#$
 *
 * 注意：
 * 登入后请
 * 更改 " 登入密码 "
 * 设定 " 交易密码 "
 * 绑定 " 二維碼 "
 * 充值接口
 * https://api.lcilf.cn/api/v1.0/Order/Pay
 * 提现接口
 * https://api.lcilf.cn/api/v1.0/Order/DF
 * 查询接口
 * https://api.lcilf.cn/api/v1.0/Order/Query
 * 商户馀额查询接口
 * https://api.lcilf.cn/api/v1.0/Merchant/Balance
 *
 * 回调IP
 * 52.246.133.52
 */
class TianJiHuaXiaPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'THX';

    /**
     * 后台登录名称
     * @var string
     */
    private static string $loginName = 'TJBET';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '110507184303';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = '355fa78826074514a31c38007d38a837';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'https://api.lcilf.cn/api/v1.0/Order/Pay';

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        // -- 客户端提交信息 --
        $merchantId = static::$merchantId; // 商户订单号
        // $desc = $params['desc'] ?? '';
        $time = $params['time'] ?? time(); // 时间戳
        $data = [
            'amount' => sprintf("%.2f", $amount),
            'callbackUrl' => self::getNotifyURL(), // 异步回调地址
            'clientIP' => $params['client_ip'],
            'currency' => 'CNY',
            'merchantNo' => $merchantId,
            'orderNo' => $params['trade_number'],
            'payType' => $type,
            'signType' => 'MD5', // 不参与签名
            'userName' => self::$loginName, // 商户前端用户名
            'payerName' => ($params['name'] ?? '天际'), // 付款用户名称
            'payerEmail' => 'tianji@gmail.com', // 付款用户邮件
            'payerPhone' => $time, // 付款用户电话
        ];
        $sign = self::getSign($data, self::$key, false); // 签名
        $data['sign'] = $sign;
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
        $client = new Client();
        $response = $client->request('POST', self::$urlSubmit, [
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data),
            'allow_redirects' => true,
            'timeout' => 2000,
            'http_errors' => true,
        ]);
        $body = $response->getBody();
        $content = trim($body->getContents());
        $body->close();

        $result = json_decode($content);
        if (isset($result->code) && $result->code == 0 && isset($result->data) && isset($result->data->payUrl)) { // 表示有可能是失败状态
            return self::jsonResult(['content' => '', 'url' => $result->data->payUrl]);
        }
        $message = $result->msg ?? '获取支付结果有误';
        return self::jsonErr($message);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['orderNo'] ?? '';
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

        $signFrom = $params['sign'];
        $data = [
            'amount' => $params['amount'],
            'code' => $params['code'],
            'dealTime' => date('YmdHis', strtotime($params['dealTime'])),
            'netAmount' => $params['netAmount'],
            'orderNo' => $params['orderNo'],
            'orderStatus' => $params['orderStatus'],
            'orderTime' => date('YmdHis', strtotime($params['orderTime'])),
            'tradeNo' => $params['tradeNo'],
        ];
        ksort($data);
        $signStr = '';
        foreach ($data as $k => $v) {
            $signStr .= "${k}=${v}&";
        }
        $signStr .= "key=" . static::$key;
        if (strtoupper(md5($signStr)) != $signFrom) {
            return false;
        }
        return true;
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
        if (!isset($params['amount']) || !is_numeric($params['amount'])) {
            return 'Error: 缺少金额字段';
        }
        if (!isset($params['orderStatus']) || !is_numeric($params['orderStatus'])) {
            return 'Error: 缺少状态字段';
        }

        $amount = $params['amount']; // 金额
        $state = $params['orderStatus']; // 0:已提交; 1:已接单; 2:超时补单; 3:订单失败; 4:交易完成; 5:未接单;
        if (!in_array($state, [1, 3])) {
            return 'Error: 状态不在范围之内';
        }

        if ($order->amount != $amount) {
            return 'Error: 支付金额不一致';
        }

        $currentTime = time();
        if ($state == 1) { // 表示订单成功
            $order->state = 1;
            $order->finished = $currentTime;
            $order->amount_paid = $amount;
            $order->updated = $currentTime;
            $order->upstream_confirmed = $currentTime; // 上游确认时间
            if ($order->save()) { // 保存订单状态
                return 'OK';
            } else {
                return 'Error: 处理状态[成功]失败, 请再次提交';
            }
        } elseif ($state == 3) {
            $order->state = 2;
            $order->updated = time();
            $order->upstream_confirmed = $currentTime; // 上游确认时间
            if ($order->save()) {
                return 'OK';
            } else {
                return 'Error: 处理状态[失败]失败, 请再次提交';
            }
        } else {
            return '未接单';
        }
    }

    /**
     * @param array $params
     * @param string $key
     * @param $emptySign
     * @return string
     */
    private static function getSign(array $params, string $key = '', $emptySign = true): string
    {
        $realArr = [];
        foreach ($params as $k => $v) {
            if ($emptySign && $v == '') { // 跳过空字符串
                continue;
            }
            if ($k == 'sign' || $k == 'signType') { // 跳过sign字段
                continue;
            }
            $realArr[$k] = $v;
        }

        ksort($realArr); // 按key进行排序
        $signArr = [];
        foreach ($realArr as $k => $v) {
            $signArr[] = "${k}=${v}";
        }
        $signString = implode('&', $signArr) . '&' . static::$keyName . '=' . (static::$key ?? $key);
        return md5($signString);
    }

    /**
     * @param string $tradeNumber
     * @param Order|NULL $order
     * @return array
     * @throws GuzzleException
     */
    public static function orderQuery(string $tradeNumber, Order $order = null): array
    {
        $url = 'https://api.lcilf.cn/api/v1.0/Order/Pay';
        $client = new Client();
        $data = [
            'merchantNo' => self::$merchantId,
            'orderNo' => $tradeNumber,
            'signType' => 'MD5',
        ];
        $signStr = "";
        $sign = md5($signStr);
        $data['sign'] = $sign;
        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data),
            'allow_redirects' => true,
            'timeout' => 5.0,
            'http_errors' => true,
            'verify' => false,
            'connect_timeout' => 5.0,
        ]);
        $body = $response->getBody();
        $content = $body->getContents();
        $body->close();

        $result = json_decode($content);
        if (!isset($result->ok) || !$result->ok || !isset($result->data) || !isset($result->data->orderStatus) || !isset($result->data->orderNo)) {
            return [
                'orderNum' => '',
                'success' => false, // 订单是否成功
            ];
        }
        return [
            'orderNum' => $result->data->orderNo,
            'success' => ($result->data->orderStatus == 1), // 订单是否成功
        ];
    }
}
