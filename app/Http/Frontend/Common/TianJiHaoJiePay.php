<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 豪杰支付:    商户号：40260
 * 商户名称:    天诚
 * 后台网址:    https://mer.heropays.com/
 * 登入帐号:    13941710888
 * 登入支付:    密码：Changeme753
 * (商户后台首次登入，google验证无需填写，登入再行绑定)
 *
 * 验证金钥：%N5CnVRZ^5huFEr5
 * 代付验证码：auF4AeHd4jLj
 * 回调IP：47.243.240.10 / 8.210.128.194
 * 👉http及https 都适用, 请择一选择更换
 *
 * 支付下单:
 * http://pay.heropays.com/api/pay/create_order
 * https://pay.heropays.com/api/pay/create_order
 *
 * 代付下单:
 * http://pay.heropays.com/api/trans/create_order
 * https://pay.heropays.com/api/trans/create_order
 * https://withdraw.heropays.com/api/trans/create_order (最新)
 * ————————————————————————--
 * 支付查询:
 * http://mer.heropays.com:3030/api/pay/query_order
 * https://mer.heropays.com:3020/api/pay/query_order
 * 代付查询:
 * http://mer.heropays.com:3030/api/trans/query_order
 * https://mer.heropays.com:3020/api/trans/query_order
 * 余额查询:
 * http://mer.heropays.com:3030/api/query/query_balance
 */
class TianJiHaoJiePay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'THJ';

    // /**
    //  * 可用支付方式, 单位: 元
    //  * @var string[]
    //  */
    // protected static array $payTypes = [
    //     '8002' => [
    //         'name' => '微信扫码',
    //         'min' => 3000,
    //         'max' => 20000,
    //         'amounts' => [],
    //     ],
    //     '8006' => [
    //         'name' => '支付宝扫码',
    //         'min' => 1000,
    //         'max' => 10000,
    //         'amounts' => [],
    //     ],
    //     '8007' => [
    //         'name' => '支付宝大额',
    //         'min' => 3000,
    //         'max' => 50000,
    //         'amounts' => [],
    //     ],
    //     '8016' => [
    //         'name' => '支付宝红包',
    //         'min' => 2000,
    //         'max' => 10000,
    //         'amounts' => [],
    //     ],
    //     // '8019' => [
    //     //     'name' => '银行转卡',
    //     //     'min' => 300,
    //     //     'max' => 3999,
    //     //     'amounts' => [],
    //     // ],
    //     '8019' => [
    //         'name' => '银行转卡',
    //         'min' => 100,
    //         'max' => 20000,
    //         'amounts' => [],
    //     ],

    // ];

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '40260';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'PI5FQ3VWPTQO1MPIL0HFHQF3CL9BFVIJE4TEG62WW3M58AXJFFGKIPWP7NUL7FYZKP43QJKRJBQP8BVBKGNXHALTKMDOIH0NUM3GZAEPOV1PJNYYLUZB3OFV09O0ACZ8';

    /**
     * 二次验证密钥
     * @var string
     */
    private static string $key2 = '%N5CnVRZ^5huFEr5';

    /**
     * @var string
     */
    private static string $appID = 'fbcc2ec10b084e478704da8c049ad8e2';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'http://pay.heropays.com/api/pay/create_order';

    /**
     * @param array $data
     * @return string
     */
    private static function getSign(array $data): string
    {
        ksort($data);
        $originStr = '';
        foreach ($data as $k => $v) {
            if ($k == 'sign' || !$v) { // 跳过sign和空值字段
                continue;
            }
            $originStr .= "${k}=${v}&";
        }
        $originStr .= "key=" . self::$key . self::$key2;
        return strtoupper(md5($originStr));
    }

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        // -- 客户端提交信息 --
        $data = [
            'mchId' => self::$merchantId,
            'appId' => self::$appID,
            'productId' => $type,
            'mchOrderNo' => $params['trade_number'],
            'currency' => 'cny',
            'amount' => intval($amount * 100),
            'clientIp' => $params['client_ip'],
            'notifyUrl' => self::getNotifyURL(), // 异步回调地址
            'subject' => '天际',
            'body' => '天际',
            // 'guest_real_name' => $params['name']
            'param1' => $params['name'],
            'param2' => $params['name'],
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
        $content = $client->post(self::$urlSubmit, ['params' => json_encode($data)]);
        $result = json_decode($content);
        if (!isset($result->retCode) || $result->retCode != 'SUCCESS') {
            return self::jsonErr($result->retMsg ?? '提交三方发生错误');
        }
        if (!isset($result->payParams)) {
            return self::jsonErr('返回参数有误');
        }
        $res = $result->payParams;
        if (isset($res->payUrl)) {
            return self::jsonResult(['content' => '', 'url' => $res->payUrl]);
        }
        return self::jsonErr('获取支付结果有误');
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
        if (!isset($params['sign'])) {
            return false;
        }

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
        if (!isset($params['amount']) || !is_numeric($params['amount']) || $params['amount'] <= 0) {
            return 'Error: 缺少金额字段';
        }

        $amount = intval($params['amount'] / 100); // 金额 = 分
        if ($order->amount != $amount) { // 金额对比
            return 'Error: 处理状态[成功]失败';
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
        $url = 'http://mer.heropays.com:3030/api/pay/query_order'; // 查单
        $data = [
            'mchId' => self::$merchantId,
            'appId' => self::$appID,
            'mchOrderNo' => $tradeNumber,
        ];
        $data['sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        // $content = $client->postByJson($url, $data);
        $content = $client->post($url, ['params' => json_encode($data)]);
        $result = json_decode($content);
        if (
            !isset($result->retCode) ||
            $result->retCode != 'SUCCESS' ||
            !isset($result->mchOrderNo) ||
            $result->mchOrderNo != $tradeNumber ||
            !isset($result->status)
        ) {
            return [
                'orderNum' => '',
                'success' => false,
            ];
        }

        // 支付状态,0-订单生成,1-支付中,2- 支付成功,3-业务处理完成(支付成功),5-支付失败
        $status = $result->status;
        return [
            'orderNum' => $result->mchOrderNo,
            'success' => $status == 3,
        ];
    }
}
