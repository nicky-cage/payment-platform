<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 8Pay
 * 类型：卡转卡
 * 通道编号：NBSF03CB
 * 個人卡代收付(支付宝、银联、快捷、微信、财付通等支付方式会造成掉单，请使用手机银行、网路银行进行转账)
 * 费率: 代收2.7%、 代付3元/笔
 * 专卡专用，不包司法、其它风控冻结解开后归还，细节请详细阅读服务条款。
 * 交易时间: 代收早上8点到晚上9点，代付延后到晚上9点半结束
 * 代收金额区间500-20,000
 * 代付金额区间3000-20,000
 * 承接量 50W起
 * 備註： 非司法類型的凍結有暫扣商戶款，因應處理程序之必要性，退款須有最長20天的等待期。
 * ************************************************************************************
 * 88支付相关资讯：
 * ************************************************************************************
 * 正式环境：
 * 商户号：XJ
 * 接口私钥：ioM3Mt0Hw1TjcrudMtns0kyt0KsyQx582KsQN6K6QY0GgVjJ7AoMS4h1p1kW
 * WEB后台登入信息
 * http://pay.1-pay.co:8028
 * 帐号：XJ
 * 密码：88888888
 * 登入后可自行修改
 *
 * 测试环境：
 * 商户号：XJ
 * 接口私钥：KgwERRLuV6OA4rvVML6S6wCrQtRYryzgrt3K6e1wYwk1QRlhSuVFbX9dHCF7
 * WEB后台登入信息
 * http://pre-pay.1-pay.co/
 * 帐号：XJ
 * 密码：88888888
 * 登入后可自行修改
 */
class TianJi88Pay extends BasePay implements PayInterface
{

    /**
     * 是否生产环璋
     */
    const IS_PRODUCT = false;

    /**
     * @var string
     */
    protected static string $channelName = 'TPY';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = 'XJ';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = '';
    private static string $keyDevelop = 'KgwERRLuV6OA4rvVML6S6wCrQtRYryzgrt3K6e1wYwk1QRlhSuVFbX9dHCF7';  // 测试环境
    private static string $keyProduct = 'ioM3Mt0Hw1TjcrudMtns0kyt0KsyQx582KsQN6K6QY0GgVjJ7AoMS4h1p1kW';  // 生产环境

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = '';
    private static string $urlDevelop = 'http://pre-pay.1-pay.co';  // 测试环境
    private static string $urlProduct = 'http://pay.1-pay.co:8028'; // 正式环境

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $amount = sprintf("%.2f", ($params['amount'] ?? 0)); // 支付金额
        // -- 客户端提交信息 --
        $time = $params['time'] ?? time(); // 时间戳
        $notifyURL = self::$notifyUrl . '/' . strtolower(self::$channelName); // 异步回调地址
        $data = [
            // 2.1 版本, 获取收款银行卡
            'pay_amount' => $amount,
            'pay_applydate' => date('Y-m-d H:i:s', intval($time)),
            'pay_productname' => '天际充值',
            'pay_memberid' => self::$merchantId,
            'pay_notifyurl' => $notifyURL,
            'pay_orderid' => $params['trade_number'],
        ];
        // $signStr = "${data['memberid']}${data['notifyurl']}${data['orderid']}${data['type']}${data['applydate']}" .
        // (self::IS_PRODUCT ? self::$keyProduct : self::$keyDevelop);
        $signStr = $data['pay_amount'] . $data['pay_applydate'] . $data['pay_productname'] .
            $data['pay_memberid'] . $data['pay_notifyurl'] . $data['pay_orderid'] .
            (self::IS_PRODUCT ? self::$keyProduct : self::$keyDevelop);
        $sign = md5($signStr);
        $data['sign'] = $sign;
        $data['pay_userip'] = $params['client_ip'];
        $data['pay_channel'] = 0;
        $data['pay_username'] = $params['name'];

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
        $domain = (self::IS_PRODUCT ? self::$urlProduct : self::$urlDevelop);
        $url = $domain . '/api/payment/request';
        // 2.1 处理, 获取收款银行卡信息
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        if (!isset($result->returncode) || $result->returncode != '00') {
            return self::jsonErr($result->message ?? '提交三方失败');
        }
        $data = [
            'custom' => true,
            'form' => [
                'trade_number' => $result->orderid,
                'merchant_no' => $result->merchant_order,
                'bank_account' => $result->bank_account,
                'bank_account_name' => $result->bank_account_name,
                'bank_code' => $result->bank_code,
                'bank_area' => $result->bank_area,
                'remark' => $result->remark,
                'pay_info_url' => $result->pay_info_url,
                'order_amount' => $result->order_amount,
            ],
        ];
        return self::jsonResult($data);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['orderid'] ?? '';
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
        // 2.1 版本
        $signStr = self::$merchantId . $params['orderid'] . $params['merchant_order'] . $params['amount'] . $params['datetime'] . $params['returncode'] .
            (self::IS_PRODUCT ? self::$keyProduct : self::$keyDevelop);
        // 2.8 版本
        //$signStr = $params['orderid'] . $params['merchant_order'] . $params['amount'] . $params['datetime'] .
        // (self::IS_PRODUCT ? self::$keyProduct : self::$keyDevelop);
        $sign = md5($signStr);
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
        if ($params['returncode'] != '00') {
            return 'Error: 格式错误';
        }
        if (!isset($params['amount']) || !is_numeric($params['amount'])) {
            return 'Error: 缺少金额字段';
        }
        $amount = $params['amount']; // 金额 = 元
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
            return 'OK';
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
        $url = (self::IS_PRODUCT ? self::$urlProduct : self::$urlDevelop) . '/payment/search/order';
        $signStr = self::$merchantId . $tradeNumber . (self::IS_PRODUCT ? self::$keyProduct : self::$keyDevelop);
        $sign = md5($signStr);
        $data = [
            'pay_memberid' => self::$merchantId,
            'pay_orderid' => $tradeNumber,
            'sign' => $sign,
        ];
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        if ($result->returncode != '00') { // 订单状态成功
            return [
                'orderNum' => $result->orderid,
                'success' => false,
            ];
        }
        return [
            'orderNum' => $tradeNumber,
            'success' => true,
        ];
    }
}
