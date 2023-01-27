<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Model\Order;
use App\Model\MerchantAPP;

/**
 * 登入网址：
 * https://hlnychina.com/pay-system/#/
 * 登入账号：天天
 * 登入密码：123456
 * 银行卡通道编号：100004
 * 通道限额：300-20000
 * 请求地址：https://hlnychina.com
 * 回调ip：103.231.30.44
 */
class XiaoNiuPay extends BasePay implements PayInterface
{
    /**
     * @var string
     */
    protected static string $channelName = 'XN';

    /**
     * 商户编号
     * @var string
     */
    public static string $merchantId = '20201226194944157103480454';

    /**
     * 商户密钥
     * @var string
     */
    public static string $key = 'c4f8b4037b394b3ba0e0223136ad5343';

    /**
     * 下单地址
     * @var string
     */
    public static string $urlSubmit = 'https://hlnychina.com';

    public static function payReadyData(array $params, array &$outParams = []): array
    {
        $type = $params['type'];
        $amount = $params['amount'] ?? 0; // 支付金额
        $callback = self::getNotifyURL(); // 接口回调地址
        $data = [
            'amount' => $amount * 100, // 金额,以分为单位；最小值100，即1元
            'orderNo' => $params['trade_number'], // 商户订单编号
            'payMode' => $type, // 支付模式：网银
            'merchantNo' => self::$merchantId, // 商户编号
            'ts' => time(), // 商户订单时间戳（秒级）
            'notifyUrl' => $callback, // 后台通知地址
            //'returnUrl' => '', // 支付完成用户返回地址 // 非必须
            //'payName' => '', // 付款人姓名, 如果参数为空, 会先跳转到系统收集付款人姓名页面 // 非必须
            //'postscript' => '', // 附言: 须全数字组合 // 非必须
        ];
        $data['sign'] = self::getSign($data, self::$key);
        $outParams = ['data' => $data];
        return $data;
    }

    /**
     * @param array $params
     * @param string $key
     * @return string
     */
    private static function getSign(array $params, string $key = ''): string
    {
        ksort($params);  // 按键顺序正序排序
        $originalStr = ''; // 拼接原始字符串
        foreach ($params as $k => $value) {
            if (!empty($value) && 'sign' != $k) {
                $originalStr .= $k . '=' . urlencode('' . $value) . '&';
            }
        }
        $originalStr = rtrim($originalStr, '&');
        $base64 = base64_encode(hash('sha256', $key . $originalStr . $key, true));
        $sign = password_hash($base64, PASSWORD_BCRYPT); // 加密生成sign
        return str_replace('$2y', '$2a', $sign); // 把抬头 $2y 替换成$2a
    }

    /**
     * @param array $data
     * @return array
     */
    public static function payResult(array $data): array
    {
        $orderUrl = self::$urlSubmit . '/pay-order/#/?';
        foreach ($data as $key => $val) {
            $orderUrl .= $key . '=' . $val . '&';
        }
        return self::jsonResult(['content' => '', 'url' => trim($orderUrl, '&')]);
    }

    /**
     * @param array $params
     * @return string
     */
    public static function notifyTradeNumber(array $params): string
    {
        return $params['orderNo'] ?? '';
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
        return self::getSign($params, self::$key) == $params['sign'];
    }

    /**
     * 返回异步结果给第三方 - 小牛支付
     * 参数名称        必须    数据类型        示例                            参数说明
     * amount        是    整数            100                            金额,以分为单位
     * merchantNo    是    字符串        20191204192421307122140114    商户编号
     * orderNo        是    字符串(<50)    201912081855183951ab02e        商户订单编号
     * payMode        是    字符串        100001                        支付模式
     * ts            是    整数            1575948756                    商户订单时间戳（秒级）
     * orderStatus    是    整数            50                            订单状态，请参考订单状态枚举
     * payNo        否    字符串        20191209194326631108714792    支付订单编号
     * payStatus    否    整数            30                            支付状态，请参考支付状态枚举
     * payTime        否    整数            1575948756                    支付成功时间（秒级）
     * sign            是    字符串        $2a$10$JwOX9nmVHrE6o8vc...    参数签名，使用BCrypt校验方法校验
     * 值    说明 - 订单状态 orderStatus
     * -20    暂无渠道，此状态下无支付状态
     * 30    支付等待中
     * -30    用户取消订单
     * -40    用户支付超时
     * -50    订单失败
     * 50    订单已完成
     * 值    说明 - 支付状态
     * 10    等待支付
     * -10    支付超时
     * -20    支付取消
     * 30    支付成功
     * -30    支付失败
     * 我方订单状态 0:待付;1:成功;2:失败;3:取消;4:拒绝;9:其他;
     * @param array $params
     * @param Order $order
     * @param MerchantApp $app
     * @return string
     */
    public static function notifyResult(array $params, Order $order, MerchantApp $app): string
    {
        if (
            !isset($params['amount']) || !is_numeric($params['amount'])
            || !isset($params['orderStatus']) || !is_numeric($params['orderStatus'])
            || !isset($params['payStatus']) || !is_numeric($params['payStatus'])
        ) {
            return 'Error: 缺少字段信息';
        }

        // 检测订单金额
        $amount = $params['amount'] / 100; // 金额 - 分 - 我方用的是元
        if ($order->amount != $amount) {
            return 'Error: 支付金额不一致';
        }

        $orderStatus = $params['orderStatus'];
        if (!in_array($orderStatus, [-20, 30, -30, -40, -50, 50])) { // 订单状态异常
            return '订单处理状态异常';
        }
        $payStatus = $params['payStatus'];
        if (!in_array($payStatus, [10, -10, -20, -30, 30])) {
            return '订单支付状态异常';
        }
        // 只有以下状态订单被视为成功, 其他都是失败
        if ($orderStatus == 50 && $payStatus == 30) { // 50:订单支付完成/30:订单状态成功
            $order->state = 1;
            $order->finished = time();
            $order->amount_paid = $amount;
            $order->updated = time();
            if ($order->save()) {  // 保存订单状态
                return 'success'; // 成功则返回 OK
            } else {
                return 'Error: 接收成功, 但我方处理失败, 请再次提交';
            }
        }

        // 订单被取消
        if ($orderStatus == -30 || $payStatus == -20) {
            $order->status = 3;
            $order->save();
            return 'success';
        }

        $order->status = 2;
        $order->remark = '订单被支付失败或者订单超时';
        $order->save();
        return 'success';
    }
}
