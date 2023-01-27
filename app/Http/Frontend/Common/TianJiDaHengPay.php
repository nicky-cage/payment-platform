<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 账号:HTJ001
 * 密码:888zxc
 * 下发密码:888zxc
 * API商户号:HTJ001
 * API密钥:2197bcd8-ed03-413f-86b1-fbf0c130e1e7
 *
 * 大亨支付
 * 可用通道状态如下：
 *
 * 卡转卡（保司法.保风控） 4.8％+5
 * 限额
 * 代收单笔限额区间 100 - 3000
 * 代付单笔限额区间 3000 - 49000
 * 下发单笔限额区间 5000 - 49000
 *
 * 通道规则
 * 1.服务时间为每日 10：00-23：30
 * 2.日量可依情况进行调整
 * 3.提现部份因水单仅保存1个月，如客户没有收到款项请赶紧与客服告知。
 * 4.包银行司法.风控（其馀诈骗.电信.杀猪不包）一般风控投诉冻结一律垫付
 *
 * 商户地址: https://customer.shpay168.com
 */
class TianJiDaHengPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TDH';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = 'HTJ001';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = '2197bcd8-ed03-413f-86b1-fbf0c130e1e7';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = '';

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = sprintf("%.2f", $params['amount'] ?? 0); // 支付金额
        // -- 客户端提交信息 --
        $notifyURL = self::getNotifyURL(); // 异步回调地址
        $data = [
            'cus_code' => self::$merchantId,
            'cus_order_sn' => $params['trade_number'], // 商戶交易編號，不能重複，必需要唯一
            'payment_flag' => $type, // 交易方式代碼;詳見 交易方式代碼
            'amount' => $amount, // 交易金額，支持小數點後兩位
            'notify_url' => $notifyURL, // 交易結果異步通知之商戶url
            'end_user_ip' => $params['client_ip'], // 商戶玩家IP
        ];
        $data['sign'] = self::getSign($data);
        $outParams = ['data' => $data];
        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    private static function getSign(array $data): string
    {
        $signStr = '';
        ksort($data);
        foreach ($data as $k => $v) {
            if ($k == 'sign' || !$v) {
                continue;
            }
            $signStr .= $k . '=' . urlencode(strval($v)) . '&';
        }
        $signStr .= 'key=' . self::$key;
        return md5($signStr);
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = 'https://api.shpay168.com/api/payment/deposit';
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        if (!isset($result->result) || !isset($result->status)) {
            return self::jsonErr($result->message ?? '发起三方支付失败');
        }
        if ($result->result != 'success' || $result->status != 200) {
            return self::jsonErr($result->message ?? '三方支付结果失败');
        }
        if (isset($result->order_info->payment_uri)) {
            return self::jsonResult(['url' => $result->order_info->payment_uri, 'auto_redirect' => false]); // 不要强制跳转
        }
        $message = $result->message ?? '获取支付结果有误';
        return self::jsonErr($message);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['cus_order_sn'] ?? '';
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
        if (!isset($params['currency_type']) || !isset($params['receive_amount']) || !is_numeric($params['receive_amount'])) {
            return 'Error: 缺少金额字段';
        }

        if (!isset($params['status'])) {
            return 'Error: 支付状态有误';
        }

        $receiveAmount = $params['receive_amount'];

        $currentTime = time();
        if ($params['status'] == 'success' && $params['receive_amount'] == $order->amount) {
            $order->state = 1;
        } else {
            $order->state = 2;
        }
        $order->finished = $currentTime;
        $order->amount_paid = $receiveAmount;
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
        $url = 'https://api.shpay168.com/api/payment/info';
        $data = [
            'cus_code' => self::$merchantId,
            'order_sn' => $tradeNumber,
        ];
        $data['sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);

        return [
            'orderNum' => isset($result->order_info) ? ($result->order_info->cus_order_sn ?? 'XX') : 'YY',
            'success' => isset($result->result)
                && $result->result == 'success'
                && $result->order_info
                && $result->order_info->receive_amount
                && $result->order_info->recieve_amount == $order->amount, // 订单是否成功 1-未支付 2-成功单
        ];
    }
}
