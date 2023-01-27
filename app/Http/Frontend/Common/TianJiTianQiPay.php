<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\HttpClient;
use App\Model\Order;
use App\Model\MerchantApp;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Utils\Str;

/**
 * 用户名称: tiantian@gmail.com（邮箱）
 * 登录密码: '@aass123'
 * 用户昵称: 天琪收付一体
 *
 * 商户后台：https://mc18.kyzfx.xyz/
 * 登入邮箱：tiantian@gmail.com
 * 登录密码：'@aass123'
 * (登录后请尽快修改密码，建议改为大小写字母 + 数字 + 特殊字符，长度不低于8位，以保证账号安全)
 *
 * 🔌接口对接信息🔌
 * 商   户  号(sid)：4ikonK6q
 * 商户密钥(key)：pkURFjNM3nP6LuHb
 * 我方回调IP：34.92.154.176
 * 商户API地址：https://api.kyzfx.xyz/
 * 代收接口：https://api.kyzfx.xyz/pay/qrorder
 * 代付接口：https://api.kyzfx.xyz/payfor/trans
 *
 * ✅商户会使用到的IP分三种✅
 * 1.登入IP：登入商户后台所需该设备IP
 * 2.提现IP：操作商户后台所有资金控管、银行卡管理、帐户管理所需设备IP
 * 3.请求IP：API接入所需服务器请求IP（添加后五分钟生效）
 * 在麻烦以上IP提前告知我方人员添加 谢谢。
 *
 * 奇奇小额代收 - 费率 3.5%+2
 * 单笔金额200～1000元任意金额
 * 下发2000起，支持U（依照当时库存）
 *
 * 充值24H，客服 24小时
 * 不包司法 ，风控解开回款，不包诈骗、需保内层
 * 日量 50w，押2w
 */
class TianJiTianQiPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'TTQ';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '4ikonK6q';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'pkURFjNM3nP6LuHb';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'https://api.kyzfx.xyz/';

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = sprintf("%.2f", $params['amount'] ?? 0); // 支付金额
        // -- 客户端提交信息 --
        $notifyURL = self::getNotifyURL(); // 异步回调地址
        $currentTime = self::getMillSeconds(); // 要 GMT 时间
        $header = [
            'sid' => self::$merchantId,
            'timestamp' => $currentTime,
            'nonce' => Str::random(16),
            'url' => '/pay/qrorder',
        ];
        $body = [
            'out_trade_no' => $params['trade_number'],
            'channel' => $type,
            'amount' => $amount,
            'currency' => 'CNY', // 中国
            'notify_url' => $notifyURL,
            'send_ip' => $params['client_ip'],
            'return_url' => '',
            'attach' => '',
        ];
        $data = [
            'header' => $header,
            'body' => $body,
        ];
        $sign = self::getSign($data);
        $data['header']['sign'] = $sign;
        $outParams = ['data' => $data];
        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    private static function getSign(array $data): string
    {
        // 有 header 和 body 的情况
        if (isset($data['header']) && isset($data['body'])) {
            $body = $data['body'];
            $header = $data['header'];
            ksort($body);
            ksort($header);

            $signStr = '';
            foreach ($header as $k => $v) {
                if ($k == 'sign') {
                    continue;
                }
                $signStr .= "$k$v";
            }
            foreach ($body as $k => $v) {
                if ($k == 'sign') {
                    continue;
                }
                $signStr .= "$k$v";
            }
            $signStr .= self::$key;
            return strtoupper(md5($signStr));
        }

        // 普通的数组情况
        $signStr = '';
        ksort($data);
        foreach ($data as $k => $v) {
            if ($k == 'sign') {
                continue;
            }
            $signStr .= "$k$v";
        }
        $signStr .= self::$key;
        return strtoupper(md5($signStr));
    }

    /**
     * @param array $data
     * @return array
     */
    private static function encodeArray(array $data): array
    {
        $arr = [];
        foreach ($data as $k => $v) {
            if ($k == 'sign') {
                $arr[$k] = $v;
            } else {
                $arr[$k] = urlencode(strval($v));
            }
        }
        return $arr;
    }

    /**
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public static function payResult(array $data): array
    {
        // $url = 'https://api.kyzfx.xyz/payfor/trans';
        $url = 'https://api.kyzfx.xyz/pay/qrorder';
        $client = HttpClient::getClient();
        $content = $client->postByArray($url, [
            // 'headers' => self::encodeArray($data['header']),
            // 'form_params' => self::encodeArray($data['body']),
            'headers' => $data['header'],
            'form_params' => $data['body'],
        ]);
        $result = json_decode($content);
        if (!isset($result->code)) {
            return self::jsonErr($result->msg ?? '发起三方支付失败');
        }
        if ($result->code != 1000 || !isset($result->pay_url)) {
            return self::jsonErr($result->msg ?? '三方支付结果失败');
        }
        $url = $result->pay_url;
        return self::jsonResult(['url' => $url]); // 不要强制跳转
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['out_trade_no'] ?? '';
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
        if (!isset($params['currency']) || !isset($params['amount']) || !is_numeric($params['amount']) || !isset($params['pay_amount']) || !is_numeric($params['pay_amount'])) {
            return 'Error: 缺少金额字段';
        }

        $amount = $params['amount'];
        $paidAmount = $params['pay_amount'];

        $currentTime = time();
        if ($amount == $paidAmount && $amount == $order->amount) {
            $order->state = 1;
        } else {
            $order->state = 2;
        }
        $order->finished = $currentTime;
        $order->amount_paid = $paidAmount;
        $order->updated = $currentTime;
        $order->upstream_confirmed = $currentTime; // 上游确认时间
        if ($order->save()) { // 保存订单状态
            return 'success';
        }
        return 'Error: 处理状态[成功]失败, 请再次提交';
    }

    /**
     * 得到毫秒
     * @return int
     */
    private static function getMillSeconds(): int
    {
        return intval(microtime(true) * 1000); // 要 GMT 时间
    }

    /**
     * @param string $tradeNumber
     * @param Order|null $order
     * @return array
     * @throws GuzzleException
     */
    public static function orderQuery(string $tradeNumber, Order $order = null): array
    {
        $url = 'https://api.kyzfx.xyz/pay/orderquery';
        $currentTime = self::getMillSeconds();
        $data = [
            'header' => [
                'sid' => self::$merchantId,
                'timestamp' => $currentTime,
                'nonce' => Str::random(16),
                'url' => '/pay/orderquery',
            ],
            'body' => [
                'out_trade_no' => $tradeNumber,
            ],
        ];
        $sign = self::getSign($data);
        $data['header']['sign'] = $sign;

        $client = HttpClient::getClient();
        $content = $client->postByArray($url, [
            'headers' => $data['header'],
            'form_params' => $data['body'],
        ]);
        $result = json_decode($content);

        $rArr = [
            'orderNum' => $tradeNumber,
            'success' => false,
        ];
        if (!isset($result->code)) {
            return $rArr;
        }
        if (
            !isset($result->amount) || !is_numeric($result->amount)
            || !isset($result->pay_amount) || !is_numeric($result->pay_amount)
            || !isset($result->out_trade_no) || $result->out_trade_no != $tradeNumber
            || !isset($result->sign)
        ) {
            return $rArr;
        }
        $sign = self::getSign((array)$result);
        if ($sign != $result->sign) {
            return $rArr;
        }

        // 当code为1000时，订单状态变量存在：
        // WAIT 等待支付
        // SUCCESS 支付成功
        // CLOSE订单关闭
        // UNCLAIMED 未认领
        // ERROR错误金额订单
        return [
            'orderNum' => $result->out_trade_no,
            'success' => $result->status == 'SUCCESS',
        ];
    }
}
