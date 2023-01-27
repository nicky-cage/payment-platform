<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 10043
 * 支付接口地址：https://ju.jupm.xyz/Index/api/pay
 * 支付查询地址：https://ju.jupm.xyz/Index/api/query
 * 回调ip：180.235.126.81,37.173.27.181,127.105.253.155,8.210.94.20,53.248.161.46,121.161.127.15
 */
class LvLvPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'PLU';
    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '10043';
    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'z1vyw1PRcRiBkfRWURa9CoWSOUtu8h3e';
    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = '';
    private static string $appId = '';

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
            'userid' => self::$merchantId,
            'innerorderid' => $params['trade_number'],
            'money' => intval($params['amount']), // 元为单位
            'type' => 2, // $params['type'], // 
            'notifyurl' => $notifyURL,
            'attach' => 'LV',
            'time' => time(),
            // 'extend' =>  '',
        ];
        /*  请根据返回的type值相应处理data。
            type=1 建议把data 生成二维码；
            type=2 建议直接输出data；
            type=3 建议进行跳转data；
            type为1时，data里的链接有时会比较长，生成二维码时要注意，不要丢失了参数；
            type为2时，data是html，内里格式不固定，可能是js跳转也可能是表单自动提交或者其他形式，请务必做web输出，别试图用任何形式的方法截取字符串；
            具体可参考demo。type为4 是 sdk串，需要app配合 
         */
        $data['sign'] = self::getSign($data);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = 'https://ju.jupm.xyz/Index/api/pay';
        $client = HttpClient::getClient();
        print_r($data);
        $content = $client->post($url, $data);
        echo "[LVLV]支付结果: ", $content, "\n";
        $result = json_decode($content);
        if (!isset($result->code) || !isset($result->type) || !isset($result->data)) {
            return self::jsonErr($result->msg ?? '支付结果有误');
        }
        if ($result->code != 'success') {
            return self::jsonErr($result->msg ?? '发起支付有误');
        }
        return self::jsonResult(['url' => $result->data, 'auto_redirect' => true]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['innerorderid'] ?? '';
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
        $amount = isset($params['money']) && is_numeric($params['money']) ? intval($params['money']) : 0;
        $status = isset($params['status']) ? intval($params['status']) : 0;
        $success = $amount == $order->amount && $status == 2;

        $currentTime = time();
        $order->state = $success ? 1 : 0;
        $order->finished = $currentTime;
        $order->amount_paid = ($success ? $amount : 0);
        $order->updated = $currentTime;
        $order->upstream_confirmed = $currentTime; // 上游确认时间
        if ($order->save()) { // 保存订单状态
            return 'ok';
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
        $url = 'https://ju.jupm.xyz/Index/api/query';
        $data = [
            'userid' => self::$merchantId,
            'innerodrderid' => $tradeNumber,
        ];
        $data['sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        return [
            'orderNum' => $tradeNumber,
            'success' => isset($result->money)
                && isset($result->status)
                && $result->status == 2
                && $result->money == intval($order->amount),
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
