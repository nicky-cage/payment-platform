<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;
use App\Common\HttpClient;

/**
 * 帐号   day168
 * 密码   mnYUXEqa
 * 网址   https://s.benchi1688.com/
 * host: api.benchi1688.com
 * 回调IP:
 * 13.251.232.230
 * 13.251.28.42
 * 通道代码: BANK_CARD
 */
class TianJiBenChiPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TBC';

    /**
     * 错误信息
     * @var string[]
     */
    private static array $errArr = [
        '1' => '余额不足',
        '2' => '提现（下发）功能未开启',
        '3' => '提交参数有误',
        '4' => '查无商户',
        '5' => '签名错误',
        '6' => '请求冲突，请重新发起',
        '7' => '查无交易',
        '8' => '订单号重复',
        '9' => '代付功能未开启',
        '10' => '金额低于下限，请提高金额',
        '11' => '金额高于上限，请降低金额',
        '12' => '通道代码有误',
        '13' => '通道维护中',
        '14' => '交易功能未开启',
        '15' => '金额有误',
        '16' => '同 IP 多笔未支付订单暂时锁定，请稍候再 试',
        '17' => '交易匹配超时，请更换金额重试',
    ];

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = 'day168';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'eM3F9K7uNqYwQqtAo25zEmzdD2IV8xEN';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'http://api.benchi1688.com/api/v1/third-party/create-transactions';

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
            $originStr .= "${k}=${v}&";
        }
        $originStr .= "secret_key=" . self::$key;
        return md5($originStr);
    }

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        // -- 客户端提交信息 --
        $time = $params['time'] ?? time(); // 时间戳
        $data = [
            'channel_code' => $type, // 通道代码
            'username' => self::$merchantId, // 金额要转化为分
            'amount' => sprintf('%.2f', $amount),
            'order_number' => $params['trade_number'],
            'notify_url' => self::getNotifyURL(), // 异步回调地址
            'real_name' => $params['name'] ?? '天际' . mt_rand(1000, 9999),
            'client_ip' => $params['client_ip'],
            'timestamp' => $time,
            'type' => $type,
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
        if (!isset($result->http_status_code) || $result->http_status_code != 201) { // 请求错误
            return self::jsonErr($result->message ?? '提交支付错误');
        }
        if (isset($result->error_code) && isset(self::$errArr[$result->error_code])) { // 固定错误代码
            return self::jsonErr(self::$errArr[$result->error_code]);
        }
        if (!isset($result->data) || !isset($result->data->status)) {
            return self::jsonErr('返回数据格式有误');
        }
        $res = $result->data;
        $message = $result->message ?? '未知';
        if (in_array($res->status, [1, 2])) {
            return self::jsonErr('订单正在处理:' . $message);
        }
        if (in_array($res->status, [6, 7, 8])) {
            return self::jsonErr('下单三方失败:' . $message);
        }
        if ($res->amount != $data['amount']) {
            return self::jsonErr('检查金额错误:' . $message);
        }
        $url = $res->casher_url ?? '';
        if (!$url) {
            return self::jsonErr('无法获取下单地址:' . $message);
        }
        return self::jsonResult(['content' => '', 'url' => $url]);
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
        if (!isset($params['data']) || !isset($params['data']['sign'])) {
            return false;
        }
        $signRequest = trim($params['data']['sign']);
        $data = $params['data'];
        $sign = self::getSign($data);
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
        if (!isset($params['data'])) {
            return 'Error: 格式不对';
        }
        $params = $params['data'];
        if (
            !isset($params['amount']) ||
            !is_numeric($params['amount']) ||
            $params['amount'] <= 0
        ) {
            return 'Error: 缺少金额字段';
        }

        $amount = intval($params['amount']); // 金额 = 元
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
        $url = 'https://api.benchi1688.com/api/v1/third-party/transaction-queries';
        $data = [
            'username' => self::$merchantId,
            'order_number' => $tradeNumber,
        ];
        $data['sign'] = self::getSign($data);
        $client = HttpClient::getClient();
        $content = $client->post($url, $data);
        $result = json_decode($content);
        if (
            !isset($result->http_status_code) ||
            $result->http_status_code != 201 ||
            !isset($result->data) ||
            !isset($result->data->order_number) ||
            $result->data->order_number != $tradeNumber ||
            !isset($result->data->status)
        ) {
            return [
                'orderNum' => '',
                'success' => false,
            ];
        }

        $status = $result->data->status;
        $success = $status == 4 || $status == 5;
        return [
            'orderNum' => $tradeNumber,
            'success' => $success, // 订单是否成功 1-未支付 2-成功单
        ];
    }
}
