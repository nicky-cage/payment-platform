<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 金星
 * 小额代收 - 费率 4.0%+2
 * 单笔金额200～1000元任意金额
 * 下发2000起，支持U（依照当时库存）
 *
 * 充值10~2点，客服 24小时
 * 包司法风控，不包诈骗、需保内层
 * 日量 50w，押2w
 *
 * 支付网关：        http://47.243.66.246:23762/deal/pay
 * 代付网关：        http://47.243.66.246:23762/deal/wit
 * 【查询订单接口】 http://47.243.66.246:23762/deal/findOrder
 *
 * 商户号 tiankong88
 * 商户回调ip
 * 8.210.34.242   47.242.50.29
 */
class TianJiJinXingPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TJX';

    // /**
    //  * 可用支付方式, 单位: 元
    //  * @var string[]
    //  */
    // protected static array $payTypes = [
    //     'BANK_R_S' => [
    //         'name' => '小额转卡',
    //         'min' => 200,
    //         'max' => 2999,
    //         'amounts' => [],
    //     ],
    //     'BANK_R' => [
    //         'name' => '大额转卡',
    //         'min' => 500,
    //         'max' => 10000,
    //         'amounts' => [],
    //     ],
    // ];

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = 'tiankong88';
    private static string $userID = 'tiankong88';
    private static string $agentDomainDev = 'https://jx.pay.pusta.click'; // 测试环境
    private static string $agentDomainPro = 'https://jx.tjtspay.com'; // 正式环境
    private static string $originDomain = 'http://8.218.11.37:32437'; // 原始域名

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = '';
    private static string $publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCIeMIHOFjHQF1k6B8d//7ydSVIq/' .
        'ZF1LQRBkQHd7OFUHEKvpMv1lzonSklhLhYHNLSf6qGJxExifyLdNE5QypVFGOOSwfy4W6Y4GjxH8McJL1xKZJhZTeW1Cen4HUJMkl' .
        '8mINXIjU/UeYSPcjxqy+LO7IOOp6QsOKLsV2pCvvm8QIDAQAB'; // 公钥
    private static string $secret = '4B4A316A8CEE4F3E95F603ABEF79981A'; // 交易密码

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = '';

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        // -- 客户端提交信息 --
        $notifyURL = self::getNotifyURL(); // 异步回调地址
        $data = [
            'appId' => self::$merchantId, // 商户号，例如：11396
            'orderId' => $params['trade_number'], // 订单号，必须唯一
            'notifyUrl' => $notifyURL,
            'pageUrl' => $params['return_url'],
            'amount' => intval($params['amount'] ?? 0),
            'passCode' => $params['type'],
            'applyDate' => date('YmdHis'), // 请求时间，时间格式：yyyyMMddHHmmss
            'userid' => $params['name'],
        ];
        $data['sign'] = self::getSign($data);
        $encryptText = self::getEncryptContent($data);
        $returnData = [
            'cipherText' => $encryptText,
            'userId' => self::$userID, // self::$userID,
        ];

        $outParams = ['data'  => $data, 'return_data' => $returnData,];

        return $returnData;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = 'http://47.243.66.246:23762/deal/pay';
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        $message = $result->message ?? '三方支付返回错误';
        $success = $result->success ?? false;
        if (!$success) {
            return self::jsonErr($message);
        }

        if (isset($result->result) && isset($result->result->returnUrl)) {
            $returnURL = '';
            $payURL = $result->result->returnUrl;
            if (env('APP_ENV') == 'dev') {
                $returnURL = str_replace(self::$originDomain, self::$agentDomainDev, $payURL);
            } else {
                $returnURL = str_replace(self::$originDomain, self::$agentDomainPro, $payURL);
            }
            return self::jsonResult(['url' => $returnURL, 'auto_redirect' => true]);
        }

        return self::jsonErr($message);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['apporderid'] ?? '';
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
        $amount = isset($params['amount']) && is_numeric($params['amount']) ? intval($params['amount']) : 0;
        $status = isset($params['status']) && in_array($params['status'], [1, 2]) ? intval($params['status']) : 0;
        $success = $amount == $order->amount && $status == 2;

        $currentTime = time();
        $order->state = $success;
        $order->finished = $currentTime;
        $order->amount_paid = ($success ? $amount : 0);
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
        $url = 'http://47.243.66.246:23762/deal/findOrder';
        $data = [
            'appId' => self::$merchantId,
            'appOrderId' => $tradeNumber,
            'type' => 'pay',
        ];
        $data['sign'] = self::getSign($data);
        $encryptText = self::getEncryptContent($data);
        $form = [
            'cipherText' => $encryptText,
            'userId' => self::$userID,
        ];
        $client = HttpClient::getClient();
        $content = $client->post($url, $form);
        $result = json_decode($content);

        return [
            'orderNum' => isset($result->orderId) ?? $tradeNumber,
            'success' => isset($result->amount)
                && isset($result->orderStatus)
                && $result->amount == $order->amount
                && $result->orderStatus == 2, // 订单是否成功 1-未支付 2-成功单
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
            if ($k == 'sign') {
                continue;
            }
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr = trim($signStr, '&') . self::$secret;
        return md5($signStr);
    }

    /**
     * @param array $data
     * @return false|string
     */
    private static function getEncryptContent(array $data): string
    {
        ksort($data);
        $buff = '';
        foreach ($data as $k => $v) {
            $buff .= $k . '=' . $v . '&';
        }
        $string = trim($buff, '&');
        $encrypted = '';
        //分段加密
        foreach (str_split($string, 117) as $chunk) {
            $encryption = openssl_public_encrypt($chunk, $str, self::getPublicKey(), OPENSSL_PKCS1_PADDING);
            if ($encryption === false) {
                return false;
            }
            $encrypted .= $str;
        }
        return base64_encode($encrypted);
    }

    /**
     * @return resource|null|string
     */
    private static function getPublicKey()
    {
        $public_key = self::$publicKey;
        $pubKey = chunk_split($public_key, 64, "\n"); //转换为pem格式的公钥
        $pubKey = "-----BEGIN PUBLIC KEY-----\n" . $pubKey . "-----END PUBLIC KEY-----\n";
        return openssl_get_publickey($pubKey);
    }
}
