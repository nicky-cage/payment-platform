<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Model\Order;
use App\Model\MerchantApp;
use App\Common\HttpClient;

/**
 * 【大聖支付-支付宝定向】
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * 费率：5.5
 * 金额：1000-2000任意金额
 * 适用类型：移动端会员  支持wap  PC
 * 下发结算方式：D0-满万下发*24小时
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * 商户后台： shanghu.83r2.com:917
 * 下单地址：http://api.83r2.com:5555
 * 通道编码：alipaydingxiang
 * 回调地址：
 * 45.196.126.253
 * 45.196.126.254
 * 45.196.126.252
 * 156.237.190.147
 * 156.237.190.151
 * 156.237.190.164
 * 账号 A94
 * 密码 FF45478152
 */
class TianJiDaShengPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TDS';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '0dfd0187f2fbccf3';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = '4046e44bfed99eda32d756bd64213003';

    /**
     * 下单地址
     * @var string
     */
    // public static string $urlSubmit = 'http://api.83r2.com:5555';
    // public static string $urlSubmit = 'http://45.196.126.218:5555';
    public static string $urlSubmit = 'http://47.242.84.208:5555';
    private static string $agentDomainDev = 'https://ds.pay.pusta.click'; // 测试环境
    private static string $agentDomainPro = 'https://ds.tjtspay.com'; // 正式环境
    private static string $originDomain = 'http://47.242.84.208:888'; // 原始域名

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        // -- 客户端提交信息 --
        $time = $params['time'] ?? time(); // 时间戳
        $data = [
            'title' => $params['trade_number'],
            'amount' => intval($amount) * 100, // 金额要转化为分
            'callback' => self::getNotifyURL(), // 异步回调地址
            'clientip' => $params['client_ip'],
            'timestamp' => $time,
            'type' => $type,
        ];
        $signStr = 'key=' . self::$merchantId .
            "&title=${data['title']}&amount=${data['amount']}&timestamp=${data['timestamp']}&secret=" . self::$key;
        $sign = md5($signStr);
        $data['sign'] = $sign;
        $outParams = ['data' => $data];
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function payResult(array $data): array
    {
        $type = $data['type'];
        unset($data['type']);
        $url = static::$urlSubmit . '/api/' . self::$merchantId . '/' . $type;
        $client = HttpClient::getClient();
        $content = $client->get($url, $data);
        if (!$content) {
            return self::jsonErr('无法获取三方支付结果');
        }

        $result = json_decode(trim($content));
        if (isset($result->ret) && $result->ret && isset($result->data) && isset($result->data->payUrl)) { // 表示有可能是失败状态
            $returnURL = '';
            if (env('APP_ENV') == 'dev') {
                $returnURL = str_replace(self::$originDomain, self::$agentDomainDev, $result->data->payUrl);
            } else {
                $returnURL = str_replace(self::$originDomain, self::$agentDomainPro, $result->data->payUrl);
            }
            return self::jsonResult(['url' => $returnURL, 'auto_redirect' => true]);
        }

        return self::jsonErr('获取支付结果有误');
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['title'] ?? '';
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

        $signStr = "title=${params['title']}&transId=${params['transId']}&amount=${params['amount']}" .
            "&transTime=${params['transTime']}&orderTime=${params['orderTime']}" .
            "&appKey=" . self::$merchantId . '&secretKey=' . self::$key;
        $signFrom = md5($signStr);
        return $params['sign'] == $signFrom;
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
        $amount = intval($params['amount']) / 100; // 金额 = 分
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
     */
    public static function orderQuery(string $tradeNumber, Order $order = null): array
    {
        $url = self::$urlSubmit . '/api/' . self::$merchantId . '/queryorder?'; // 查单地址
        $time = time();
        $signStr = "key=" . self::$merchantId . "&title=${tradeNumber}&timestamp=${time}&secret=" . self::$key;
        $sign = md5($signStr);
        $url .= "title=${tradeNumber}&timestamp=${time}&sign=${sign}";
        $content = file_get_contents($url);
        $result = json_decode($content);
        if (!$result || !isset($result->result) || !isset($result->data) || !isset($result->data->appOrderId) || !isset($result->data->status)) {
            return [
                'orderNum' => '',
                'success' => false,
            ];
        }

        return [
            'orderNum' => $result->data->appOrderId,
            'success' => $result->data->status == 2, // 订单是否成功 1-未支付 2-成功单
        ];
    }
}
