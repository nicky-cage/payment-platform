<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * =====基本信息=====
 * 商户名称：天际888
 * 商户后台地址：https://mchc9htfn.zpqvj.xyz
 * 商户登录名：tianji888
 * 商户初始登录密码：123456
 * 商户初始提现密码：123456
 * 提示：强烈建议您在正式使用前，在商户后台自助绑定谷歌身份验证器
 * ===============
 * =====API接口信息=====
 * 网关根地址（无法直接访问根地址，完整接口地址需要继续拼接，详见接口文档）：
 * https://apin7oqm6.zpqvj.xyz
 * 
 * MerchantId（商户ID）：
 * 72388
 * 
 * Md5Key（建议您在正式使用前，在商户后台自助重置Md5Key，重置后请告知您的技术人员）：
 * k60dEizHv9ekBaq2ae5J7Nl751i89ggd6PDR1
 * 
 * 我方用于回调(通知支付成功)的发起IP（请技术人员严格判断）：
 * 103.71.176.230
 * 103.35.149.143
 * 
 * 已开通渠道（PayTypeId）：
 * PayTypeId----支付名称----费率----限额
 * kzk-----卡转卡-------2.5-------500-50000
 */
class TianJiC1Pay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TC1';

    /**
     * 可用支付方式, 单位: 元
     * @var string[]
     */
    protected static array $payTypes = [
        'kzk' => [
            'name' => '银行卡转卡',
            'min' => 500,
            'max' => 50000,
            'amounts' => [],
        ],
    ];

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '72388';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'k60dEizHv9ekBaq2ae5J7Nl751i89ggd6PDR1';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'https://apin7oqm6.zpqvj.xyz';

    /**
     * 得到处理过之后的数据
     * @param array $params
     * @return array|mixed
     */
    public static function payReadyData(array $params, array &$outParams = []): array
    {
        // -- 客户端提交信息 --
        $notifyURL = self::$notifyUrl . '/' . strtolower(self::$channelName); // 异步回调地址
        $data = [
            'Amount' => sprintf("%.2f", floatval($params['amount'])),
            'Ip' => $params['client_ip'],
            'MerchantId' => self::$merchantId,
            'MerchantUniqueOrderId' => $params['trade_number'],
            'NotifyUrl' => $notifyURL,
            'PayTypeId' => $params['type'],
            'Remark' => $params['trade_number'],
            'ReturnUrl' => $params['return_url'],
            'X_A_ClientRealName' => $params['name'],
        ];
        $data['Sign'] = self::getSign($data);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = trim(self::$urlSubmit, '/') . '/InterfaceV5/CreatePayOrder/';
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        echo "[C1]支付结果: ", $content, "\n";
        $result = json_decode($content);
        if (!isset($result->Code) || !isset($result->RealAmount) || !isset($result->Url)) {
            return self::jsonErr('');
        }

        if ($result->Code != 0) {
            return self::jsonErr($result->MessageForUser ?? '发起支付有误');
        }

        return self::jsonResult(['url' => $result->Url, 'auto_redirect' => true]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['MerchantUniqueOrderId'] ?? '';
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool
    {
        $sign = self::getSign($params);
        return $params['Sign'] == $sign;
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
        $amount = isset($params['Amount']) && is_numeric($params['amount']) ? intval($params['Amount']) : 0;
        $status = isset($params['PayOrderStatus']) && in_array($params['PayOrderStatus'], [0, 100, -90]) ? intval($params['PayOrderStatus']) : 0;
        $success = $amount == $order->amount && $status == 100;

        $currentTime = time();
        $order->state = $success;
        $order->finished = $currentTime;
        $order->amount_paid = ($success ? $amount : 0);
        $order->updated = $currentTime;
        $order->upstream_confirmed = $currentTime; // 上游确认时间
        if ($order->save()) { // 保存订单状态
            return 'SUCCESS';
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
        $url = 'http://47.243.66.246:23762/deal/findOrder';
        $url = trim(self::$urlSubmit, '/') . '/InterfaceV5/QueryPayOrder/';
        $data = [
            'MerchantId' => self::$merchantId,
            'MerchantUniqueOrderId' => $tradeNumber,
            'Timestamp' => date('YmdHis'),
        ];
        $data['Sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);

        return [
            'orderNum' => $tradeNumber,
            'success' => isset($result->RealAmount)
                && isset($result->Code)
                && $result->Code == 0
                && $result->RealAmount == $order->amount
                && $result->PayOrderStatus == 100,
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
            if ($k == 'sign' || $k == 'X_A_ClientRealName') {
                continue;
            }
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr = trim($signStr, '&') . self::$key;
        return strtolower(md5($signStr));
    }
}
