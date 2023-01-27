<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 通道编码：QX992061
 * 商户后台：http://mct.jiekepay.top/m8fetyv
 * 账号： HH
 * 密码： 222333
 * 商户编号：13897554 
 * 密钥：b3fe997eb1e4aba6dd5f714f3252f2e7 
 * 下单网关：http://api.jiekeshop.com/v1/htpay/ht-create-order
 * 查单网关：http://api.jiekeshop.com/v1/htpay/ht-query-order
 * 回调ip：154.23.176.42，154.23.176.188，154.23.176.193
 */
class LvV2Pay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'LV2';
    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '13897554';
    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'b3fe997eb1e4aba6dd5f714f3252f2e7';
    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'http://api.jiekeshop.com/v1';

    /**
     * 得到处理过之后的数据
     * @param array $params
     * @return array|mixed
     */
    public static function payReadyData(array $params, array &$outParams = []): array
    {
        // -- 客户端提交信息 --
        $notifyURL = self::getNotifyURL();
        $data = [
            'merchant_no' => self::$merchantId,
            'pay_code' => $params['type'],
            'order_amount' => sprintf("%.2f", floatval($params['amount'])), // 订单金额(元),保留 2 位小数
            'order_no' => $params['trade_number'],
            'callback_url' => $notifyURL, // 以分为单位
            'attach' => 'LV2',
            'ts' => time() * 1000,
        ];
        $data['sign'] = self::getSign($data);
        print_r($data);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = self::$urlSubmit. '/htpay/ht-create-order';
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        echo "[LV2]支付结果: ", $content, "\n";
        $result = json_decode($content);

        if (!isset($result->code) || !isset($result->success)) { 
            return self::jsonErr('获取格式有误');
        }
        if ($result->code != 200 || !$result->success) { 
            return self::jsonErr('结果有误:'. ($result->msg ?? '请求结果有误'));
        }
        if (!isset($result->data)) { 
            return self::jsonErr('缺少Data:'. ($result->msg ?? '缺少处理结果'));
        }

        $data = is_array($result->data) ? (object) $result->data : $result->data;
        if (!isset($data->pay_url)) { 
            return self::jsonErr('无法获取支付地址');
        }
        return self::jsonResult(['url' => $data->pay_url]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['order_no'] ?? '';
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool
    {
        if (isset($params['platform_no'])) { 
            unset($params['platform_no']);
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
        $amount = isset($params['order_amount']) && is_numeric($params['order_amount']) ? intval($params['order_amount']) : 0;
        $status = isset($params['status']) && in_array($params['status'], [0, 1, 2, 6, 7, 8]) ? intval($params['status']) : 0;
        $success = $amount == $order->amount && $status == 6;

        $currentTime = time();
        $order->state = ($success ? 1 : 2);
        $order->finished = $currentTime;
        $order->amount_paid = ($success ? $amount : 0);
        $order->updated = $currentTime;
        $order->upstream_confirmed = $currentTime; // 上游确认时间
        if ($order->save()) { // 保存订单状态
            return 'success';
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
        $url = self::$urlSubmit. '/htpay/ht-query-order';
        $data = [
            'merchant_no' => self::$merchantId,
            'order_no' => $tradeNumber,
            'ts' => time() * 1000,
        ];
        $data['sign'] = self::getSign($data);
        print_r(['query' => $data]);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        print_r($result);

        $data = isset($result->data) ? (is_array($result->data) ? (object) $result->data : $result->data) : null;
        return [
            'orderNum' => $tradeNumber,
            'success' => isset($result->code) && $result->code == 200
                && isset($result->success) && $result->success
                && isset($data->amount) && intval($data->amount) == $order->amount
                && isset($data->order_no) && $data->order_no == $tradeNumber
                && isset($data->order_status) && $data->order_status == 6,
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
        return md5($signStr);
    }
}
