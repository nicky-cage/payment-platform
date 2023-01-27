<?php

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\DbConnection\Db;
use App\Common\Func;

class UnityPay extends BasePay implements PayInterface
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
    protected static string $channelName = 'UN';                     // aes加密key
    private static string $aesKey = 'xkKDhEeDMMkxnJY7';                            // 渠道名称
    private static string $agentDomainPro = 'https://lv.tjtspay.com';     // 正式环境
    private static string $agentDomainDev = 'https://lv.pay.pusta.click';   // 测试环境
    private static string $originDomain = 'http://z1z2z200play.coco-co.cc'; // 发起支付原始域名

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $amount = intval($params['amount'] ?? 0); // 支付金额
        $notifyURL = self::getNotifyURL(); // 异步通知地址
        $data = [
            'payNo' => $params['trade_number'], // 订单编号
            'bizCode' => '', // 业务线
            'payToolCode' => '', // 支付工具代码
            'orgCode' => '',
            'amount' => $amount, // 金额
            'userId' => '', // 用户编号 
            'notifyUrl' => $notifyURL, // 异步通知地址
            // 'extraData' => '', // 附加信息 - 非必填
            // 'openId' => '',
            // 'productDesc' => '',
            // 'appId' => '',
            // 'tradeType' => '',
        ];

        // 组装签名数据
        $aesEnString = self::getEncryptContent($data);
        $sign = md5($aesEnString);

        $returnData = [
            'sign' => $sign,
            'bizContents' => $aesEnString,
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
        ksort($data);
        $signData = json_encode($data);
        return openssl_encrypt($signData, 'AES-128-ECB', self::$aesKey, 0, '');
    }

    /**
     * @param string $dataStr
     * @param array
     */
    private static function getDecodeContent(string $dataStr): array
    {
        $jsonArr = trim(openssl_decrypt($dataStr, 'AES-128-ECB', self::$aesKey, 0, ''));
        return (array)json_decode(trim($jsonArr));
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        $url = trim(self::$urlSubmit, '/') . '/';
        $client = HttpClient::getClient();
        $content = $client->postByBody($url, $data);
        $result = json_decode($content);
        if (!isset($result->code) || !isset($result->sign) || !isset($result->bizContents)) {
            return self::jsonErr('提交三方失败');
        }
        if ($result->code != 0) { // 只有等于0才是成功
            return self::jsonErr('发起支付失败');
        }

        $ret = self::getDecodeContent($result->bizContents); // 解码返回信息
        if (!$ret) {
            return self::jsonErr('获取支付内容无效');
        }

        $obj = (object) $ret;
        if (
            !isset($obj->code) || $obj->code != '0' ||
            !isset($obj->tradeStatus) || $obj->tradeStatus != 20 ||
            !isset($obj->data) || !isset($obj->data->payUrlCode)
        ) {
            return self::jsonErr('生成支付失败');
        }

        $payURL = $obj->data->payUrlCode;
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

        $jsonArr = trim(openssl_decrypt($dataStr, 'AES-128-ECB', self::$aesKey, 0, ''));
        $jsonData = (array)json_decode(trim($jsonArr));
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
