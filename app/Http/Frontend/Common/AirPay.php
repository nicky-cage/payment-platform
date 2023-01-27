<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;
use App\Common\HttpClient;

/**
 * 第三方金流名称：
    平台网址：http://pay.qlairpay.com:10240/sj
    登录名：test
    登录密码：aa123
    提供一组第三方资料供技术测试
    商户号：10
    密钥：keuBCcqAvaHEbhstPSYvXgUSMErYUvSW
    回调IP：85.208.118.27
    代收下单接口：http://pay.qlairpay.com:10240/api/Order/SubmitOrder
    代收查单接口：http://pay.qlairpay.com:10240/api/Order/QueryOrder
    代收余额接口：http://pay.qlairpay.com:10240/api/Order/QueryBalance
    回调IP：85.208.118.27

    支付方式 网银转卡 限额11~3000
    Kevin🔥, [Jan 27, 2023 at 8:29:38 PM]:
    代收 1001 代付1000
    文档里面都有标注

    代收卡转卡，限额11-3000🔥
    代付金额1000-19999🔥
    代收入款资金 付款人与会员姓名不符合 500以上要原路退回
 */
class AirPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'AIR';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '10';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'keuBCcqAvaHEbhstPSYvXgUSMErYUvSW';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'http://api.benchi1688.com/api/v1/third-party/create-transactions';
    public static string $urlCreateOrder = 'http://pay.qlairpay.com:10240/api/Order/SubmitOrder';       // 创建订单
    public static string $urlQueryOrder = 'http://pay.qlairpay.com:10240/api/Order/QueryOrder';         // 查询订单
    public static string $urlQueryBalance = 'http://pay.qlairpay.com:10240/api/Order/QueryBalance';     // 查询余额

    /**
     * @param array $data
     * @return string
     */
    private static function getSign(array $data): string
    {
        ksort($data);
        $originStr = '';
        foreach ($data as $k => $v) {
            if ($k == 'sign') {
                continue;
            }
            $originStr .= "{$k}={$v}&";
        }
        $signStr = trim($originStr, '&') . "key=" . self::$key;
        return strtoupper(md5($signStr));
    }

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        // -- 客户端提交信息 --
        $time = $params['time'] ?? time(); // 时间戳
        $data = [
            'MerchantID' => self::$merchantId, // 通道代码
            'orderID' => $params['trade_number'], // 金额要转化为分
            'Date' => date('YmdHis'), // yyyyMMddHHmmss
            'NotifyUrl' => self::getNotifyURL(),
            'CallBackUrl' => $params['return_url'],
            'Amount' => $params['amount'], // 异步回调地址
            'MerchantNumber' => mt_rand(1000, 9999),
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
        $content = $client->post(self::$urlSubmit, $data);
        $result = json_decode($content);
        if (!isset($result->Code) || $result->Code != 'Success') { // 请求错误
            return self::jsonErr($result->Msg ?? '提交支付错误');
        }
        if (!isset($result->Url)) {
            return self::jsonErr('无法获取下单地址:');
        }
        return self::jsonResult(['content' => '', 'url' => $result->Url]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        if (isset($params['data'])) {
            return $params['data']['order_number'] ?? '';
        }
        return '';
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool
    {
        if (!isset($params['MerchantID']) || !isset($params['Sign'])) {
            return false;
        }
        $signRequest = trim($params['Sign']);
        $sign = self::getSign($params);
        return $signRequest == $sign;
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
        if (!isset($params['MerchantID']) || !isset($params['OrderID'])) {
            return 'Error: 格式不对';
        }
        $params = $params['data'];
        if (
            !isset($params['Amount']) ||
            !is_numeric($params['Amount']) ||
            $params['Amount'] <= 0
        ) {
            return 'Error: 缺少金额字段';
        }

        $amount = intval($params['Amount']); // 金额 = 元
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
        $url = '';
        $data = [
            'MerchantID' => self::$merchantId,
            'OrderID' => $tradeNumber,
        ];
        $data['sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        if (!isset($result->Code) || $result->Code != 'Success') {
            return [
                'orderNum' => '',
                'success' => false,
            ];
        }

        $success = $result->Amount == $order->amount && $result->Status == 1;
        return [
            'orderNum' => $tradeNumber,
            'success' => $success, // 订单是否成功 1-未支付 2-成功单
        ];
    }
}
