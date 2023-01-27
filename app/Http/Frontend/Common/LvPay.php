<?php

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\DbConnection\Db;
use App\Common\Func;

class LvPay extends BasePay implements PayInterface
{
    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '34gcfjfcf0';
    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'ab726d4b4bf24358b7a5da8f4d317bb8';         // 商户密钥/md5
    public static string $urlSubmit = 'https://xypay.longlong.today/';      // 提交域名
    protected static string $channelName = 'LV';                     // aes加密key
    private static string $aesKey = 'xkKDhEeDMMkxnJY7';                            // 渠道名称
    private static string $agentDomainPro = 'https://lv.tjtspay.com';     // 正式环境
    private static string $agentDomainDev = 'https://lv.pay.pusta.click';   // 测试环境
    private static string $originDomain = 'http://z1z2z200play.coco-co.cc'; // 发起支付原始域名

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $amount = sprintf("%.2f", ($params['amount'] ?? 0)); // 支付金额
        // -- 客户端提交信息 --
        // $notifyURL = self::getNotifyURL() . '/' . strtolower(self::$channelName); // 异步回调地址
        $notifyURL = self::getNotifyURL();
        $data = [
            "channel" => $params['type'], // 类型
            "orderNo" => $params['order_number'], // 订单
            "price" => ($amount * 100),
            "notifyUrl" => $notifyURL,
            "returnUrl" => $params['return_url'],
            "returnType" => 'json',
            "mark" => '支付平台',
        ];

        // 组装签名数据
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= "${k}=${v}&";
        }
        $str .= 'key=' . self::$key;
        $sign = strtolower(md5($str));
        $data['sign'] = $sign;
        $aesString = self::getEncryptContent($data); // 获得加密数据

        $returnData = [
            'data' => $aesString,
            'apiCode' => self::$merchantId,
        ];

        $outParams = ['data' => $data, 'return_data' => $returnData];
        return $returnData;
    }

    /**
     * @param array $data
     * @return bool|string
     */
    private static function getEncryptContent(array $data): string
    {
        $signData = json_encode($data);
        return openssl_encrypt($signData, 'AES-128-ECB', self::$aesKey, 0, '');
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = trim(self::$urlSubmit, '/') . '/services/transcctionservice/api/payOrder/payment';
        $client = HttpClient::getClient();
        $content = $client->postByBody($url, $data);
        $result = json_decode($content);
        if (!isset($result->code)) {
            return self::jsonErr($result->msg ?? '提交三方失败');
        }
        if ($result->code != 1000) {
            return self::jsonErr($result->msg ?? '发起支付失败');
        }
        if (!isset($result->data) || !isset($result->data->payUrl)) {
            return self::jsonErr($result->msg ?? '发起支付有误');
        }

        $payURL = $result->data->payUrl;
        $returnURL = '';
        if (env('App_ENV') == 'dev') { // 开发环境 - 测试环境
            $returnURL = str_replace(self::$originDomain, self::$agentDomainDev, $payURL);
        } else { // 开发环境 - 正式环境
            $returnURL = str_replace(self::$originDomain, self::$agentDomainPro, $payURL);
        }

        return self::jsonResult([
            'auto_redirect' => true,
            'url' => $returnURL,
        ]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['downstreamNo'] ?? '';
    }

    /**
     * @param string $dataStr
     * @param array
     */
    private static function getDecodeContent(string $dataStr): array
    {
        $jsonArr = trim(openssl_decrypt($dataStr, 'AES-128-ECB', self::$aesKey, 0, ''));
        $jsonData = (array)json_decode(trim($jsonArr));
        if (!isset($jsonData['sign'])) {
            return [];
        }
        return $jsonData;
    }

    /**
     * @param array $params
     * @return bool
     */
    public static function notifyCheckSign(array $params): bool
    {
        if (!isset($params['data'])) { // 数据, 用于解密
            return false;
        }

        $dataStr = $params['data'];
        $apiCode = $params['apiCode'];
        if ($apiCode != self::$merchantId) {
            return false;
        }

        // $jsonArr = trim(openssl_decrypt($dataStr, 'AES-128-ECB', self::$aesKey, 0, ''));
        // $jsonData = (array)json_decode(trim($jsonArr));
        $jsonData = self::getDecodeContent($dataStr);
        if (!isset($jsonData['sign'])) {
            return false;
        }

        $signFrom = $jsonData['sign'];
        unset($jsonData['sign']);
        ksort($jsonData);
        $str = '';
        foreach ($jsonData as $k => $v) {
            $str .= "${k}=${v}&";
        }
        $str .= 'key=' . self::$key;
        $sign = strtolower(md5($str));

        return $signFrom == $sign;
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
        $dataStr = $params['data'];
        $jsonArr = trim(openssl_decrypt($dataStr, 'AES-128-ECB', self::$aesKey, 0, ''));
        $jsonData = json_decode(trim($jsonArr));

        if ($jsonData->status != '1') {
            return 'Error: 格式错误';
        }
        if (!isset($jsonData->money) || !is_numeric($jsonData->money)) {
            return 'Error: 缺少金额字段';
        }
        $amount = $jsonData->money / 100; // 金额 = 分
        if ($order->amount != $amount) { // 金额对比
            return 'Error: 处理状态[成功]失败, 请再次提交';
        }
        //点位计算
        $channelInfo = Db::table("merchant_rate")->where("channel_id", $order->channel_id)->where("type", $order->type)->first();
        $currentTime = time();
        $order->state = 1; // 状态 
        $order->finished = $currentTime;    // 完成时间
        $order->amount_paid = $amount;      // 实付金额
        $order->updated = $currentTime;     // 当前时间
        $order->upstream_confirmed = $currentTime; // 上游确认时间

        $order->platform_rate = $channelInfo->platform_rate; //平台点位
        $order->rate = $channelInfo->lower_rate; //最终点位
        $order->parent_id = $channelInfo->parent_id; //父级ID
        $order->parent_rate = $channelInfo->parent_rate; //父级点位
        $str = '';
        $order->path = Func::getParentPath($order->merchant_id, $order->channel_id, $order->type, $str); //无限级路径，。记录每级当时的点位

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
        $url = trim(self::$urlSubmit, '/') . '/services/transcctionservice/api/cashOrder/cashOrder/query';

        $data = [
            'downstreamNo' => $tradeNumber,
            'time' => time(),
        ];
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= "${k}=${v}&";
        }
        $str .= 'key=' . self::$key;
        $sign = strtolower(md5($str));
        $data['sign'] = $sign;

        $aesString = self::getEncryptContent($data); // 获得加密数据
        $submitData = [
            'apiCde' => self::$merchantId,
            'data' => $aesString,
        ];

        $client = HttpClient::getClient();
        $content = $client->postByBody($url, $submitData);
        $result = json_decode($content);

        if (
            !isset($result->code) || $result->code != 1000 ||
            !isset($result->data) ||
            !isset($result->data->downstreamNo) || $result->data->downstreamNo != $tradeNumber ||
            !isset($result->data->status) || $result->data->status != 1
        ) {
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

    public static function balance(array $params): array
    {
        return [];
    }
}
