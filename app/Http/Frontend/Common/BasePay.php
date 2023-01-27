<?php

declare(strict_types=1);

namespace App\Http\Frontend\Common;

use App\Common\Utils;
use App\Http\Common\BaseJsonTrait;
use App\Model\{Channel, ChannelPayment, ErrorLog, MerchantApp, Order};
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;

class BasePay
{
    /**
     * 用于签名的key
     * @var string
     */
    public static string $key = '';
    /**
     * 用于签名的key的名称
     * @var string
     */
    public static string $keyName = 'key';
    /**
     * 订单编号渠道前缀
     * @var string
     */
    protected static string $channelName = '';
    /**
     * 异步回调地址
     * @var string
     */
    protected static string $notifyUrl = 'https://api.pay.pusta.click/payments/notify';
    protected static string $notifyURLDev = 'https://api.pay.pusta.click/payments/notify';
    protected static string $notifyURLPro = 'https://p.tjtspay.com/payments/notify';

    /**
     * 目前可用的支付渠道
     * @var string[]
     */
    private static array $channels = [
        'TDS' => TianJiDaShengPay::class,   // 天际 - 大圣支付
        'TBC' => TianJiBenChiPay::class,    // 天际 - 奔驰支付
        'THJ' => TianJiHaoJiePay::class,    // 天际 - 豪杰/信达支付
        'TPY' => TianJi88Pay::class,        // 天际 - 88Pay
        'TDH' => TianJiDaHengPay::class,    // 天际 - 大亨支付/小亨支付
        'TJX' => TianJiJinXingPay::class,   // 天际 - 金星支付
        'TBX' => TianJiBaXingPay::class,    // 天际 - 八星支付
        'TTQ' => TianJiTianQiPay::class,    // 天际 - 天琪支付
        'LV' => LvPay::class,               // 天际 - LV支付
        'UN' => UnityPay::class,            // 统一支付
        'TC1' => TianJiC1Pay::class,        // 天际
        'PHJ' => HuangJiaPay::class,        // 皇家支付
        'PLU' => LvLvPay::class,            // LVLV
        'LV2' => LvV2Pay::class,            // lv - 新 qq 支付
    ];
    use BaseJsonTrait;

    /**
     * 发起支付
     *
     * @param array $params
     * $params 参数说明:
     * 1. 必须包含的参数: channel/type/amount/order_number
     * 2. 可选包含的参数: extend, 如果是网银必须包含此参数
     * @param MerchantApp $app
     * @return array
     */
    public static function pay(array $params, MerchantApp $app): array
    {
        // 检测支付通道是否存在
        $channel = $params['channel'];
        if (!isset(self::$channels[$channel])) { // 判断支付渠道是否存在
            return self::jsonErr('指定支付渠道有误');
        }
        $amount = $params['amount'] ?? 0;
        if ($amount <= 0 || $amount > 900000) { // 金额验证
            return self::jsonErr('订单金额有误');
        }
        $orderNumber = $params['order_number'] ?? ''; // 订单号码验证
        if ($orderNumber == '' || strlen($orderNumber) < 6 || strlen($orderNumber) > 32) {
            return self::jsonErr('订单编号格式有误');
        }
        $appId = $params['app_id'] ?? '';
        if ($appId == '' || !is_numeric($appId)) {
            return self::jsonErr('缺少应用编号');
        }

        // 查询订单是否存在
        $order = Order::query()->where('order_number', $orderNumber)->first();
        if ($order && isset($order->order_number)) { // 如果订单已经存在
            // 如果订单在1分钟以内, 则使用上次生成的地址
            $submitTime = intval($params['time']); // 提交时间
            if (time() - $submitTime < 120) { // 如果是在一分半之内提交的数据
                if ($order->pay_data != '') {
                    $payResult = json_decode($order->pay_data);
                    return is_object($payResult) ? self::jsonObject($payResult) : self::jsonArray($payResult);
                }
            }
            return self::jsonErr('请不要重复提交订单: ' . $orderNumber);
        }

        // 查找上游渠道是否存在
        $upstream = Channel::query()->where('code', $channel)->first();
        if (!$upstream) {
            return self::jsonErr('缺少上游渠道信息:' . $channel);
        }

        // -- 从数据库当中读取支付限额
        $channelID = $upstream->id; // 渠道ID
        $type = $params['type']; // 支付方式
        $payInfo = ChannelPayment::query()->where(['channel_id' => $channelID, 'code' => $type])->first(); // 支付信息
        if (!$payInfo) {
            return self::jsonErr('缺少支付方式:' . $type);
        }
        // - 判断支付金额区间
        if ($payInfo->amounts && !in_array($amount, array_map(function ($r) {
                return trim($r);
            }, explode(',', $payInfo->amounts)))) {
            return self::jsonErr('支付金额必须在 [' . $payInfo->amounts . '] 之间');
        }
        if ($amount < $payInfo->amount_min || $amount > $payInfo->amount_max) { // 金额必须在固定区间之内
            return self::jsonErr('支付金额必须在 ' . $payInfo->amount_min . ' - ' . $payInfo->amount_max . ' 之间');
        }

        $class = self::getChannelClass($channel);
        if (!$class) {
            return self::jsonErr('无法指定支付渠道或者类型');
        }

        // -- 服务端生成信息 交易单号 --
        $tradeNumber = Utils::getOrderNumber($channel);
        $params['trade_number'] = $tradeNumber; // 统一订单号
        $insertData = [];
        $submitData = $class::payReadyData($params, $insertData); // 准备下单要提交数据
        if (!$insertData) {
            $insertData = $submitData;
        }

        // -- 生成订单并写入到数据库当中
        $currentTime = time();
        $data = [
            'merchant_id' => $app->merchant_id, // 商户编号
            'order_number' => $params['order_number'], // 商户订单号码 - 下游
            'trade_number' => $tradeNumber, // 交易单号 - 上游
            'channel_id' => $upstream->id, // 渠道编号
            'amount' => $amount, // 订单金额
            'ip' => $params['client_ip'] ?? '192.168.0.1', // 来源ip
            'app_id' => $app->id, // 应用编号
            'created' => $currentTime, // 当前时间
            'updated' => $currentTime, // 最后修改
            'return_url' => $params['return_url'] ?? '', // 回调地址
            'pay_data' => json_encode($submitData), // 本地处理支付结果
            'submit_data' => json_encode($insertData), // 提交支付数据
            'type' => $type, // 编码
            'notify_url' => $params['notify_url'] ?? $app->notify_url, // 异步通知结果 - 向下游的
            'upstream_money' => $amount * (float)$payInfo->rate / 100,
            'payment_id' => $payInfo->id,
        ];
        if (!Db::table('orders')->insert($data)) { // 写入订单数据表
            return self::jsonErr('生成订单失败');
        }
        $result = $class::payResult($submitData);
        $resultJson = json_encode($result);
        // 修改订单信息, 将上次支付结果详情写入到订单记录当中
        Db::table('orders')->where('order_number', $orderNumber)->update(['pay_result' => $resultJson]);
        return $result;
    }

    // ---------------------------------------------------- 支付下单 ----------------------------------------------------- //

    /**
     * 得到要使用的支付渠道类
     * @param string $channel
     * @return PayInterface|null
     */
    protected static function getChannelClass(string $channel): ?PayInterface
    {
        $className = self::$channels[$channel] ?? '';
        if ($className == '') {
            return null;
        }
        return new $className();
    }

    /**
     * 处理回调
     * @param array $params 此时已经包含支付方式
     * @param RequestInterface $request
     * @return string
     */
    public static function notify(array $params, RequestInterface $request): string
    {
        $channel = $params['channel'] ?? '';
        unset($params['channel']);
        if (!isset(self::$channels[$channel])) { // 判断是否包含此支付方式
            return self::notifyFailure('Error: Channel Code', $request);
        }

        // 获取所需参数
        $requestURL = $params['request_url'];
        unset($params['request_url']);
        $requestIP = $params['request_ip'];
        unset($params['request_ip']);

        // -- 业务处理
        $class = self::getChannelClass($channel); // 获取相应的支付处理类
        $tradeNumber = $class::notifyTradeNumber($params); // 获取交易单号 - 商户订单号 - 是我们的订单号
        if ($tradeNumber == '') { // 验证单号
            return $class::notifyFailure('Error: No Order Number', $request);
        }

        if (!$class::notifyCheckSign($params)) { // 验证签名
            return $class::notifyFailure('Error: Sign Failure', $request);
        }
        // 获取订单信息
        $row = Order::query()->where('trade_number', $tradeNumber)->first();
        if (!$row) {
            return $class::notifyFailure('Error: No Order Info', $request); // 提前返回失败结果
        }
        // 获取相应应用信息
        $app = MerchantApp::query()->find($row->app_id); // 依据app-id 得到相关的app信息
        if (!$app) {
            return $class::notifyFailure('Error: No App Info', $request); // 没有查找到对应信息
        }

        $result = $class::notifyResult($params, $row, $app); // 处理异步通知
        // 写上游戏通知处理结果
        $data = [
            'merchant_id' => $app->merchant_id,
            'app_id' => $app->id,
            'trade_number' => $tradeNumber,
            'channel_id' => $row->channel_id,
            'request_url' => $requestURL,
            'request_ip' => $requestIP,
            'reply' => $result,
            'created' => time(),
            'remark' => '',
            'request_data' => json_encode($params),
        ];
        Db::table('notify_ups')->insert($data);
        return $result;
    }

    // ---------------------------------------------------- 支付回调 ----------------------------------------------------- //

    /**
     * 提前返回失败通知
     * @param string $message
     * @param RequestInterface|null $request
     * @return mixed
     */
    public static function notifyFailure(string $message = 'Process failure', RequestInterface $request = null): string
    {
        $requestIP = Utils::clientIP($request);
        $path = $request->getUri()->getPath(); // 依据请求url来判断哪个支付方式
        $r = new ErrorLog();
        $r->error_type = 1;
        $r->data = json_encode($request->all());
        $r->error = $message;
        $r->ip = $requestIP;
        $r->url = $path;
        $r->save();
        return $message;
    }

    // ---------------------------------------- 订单查询 -------------------------------------------------------------

    /**
     * 查询订单
     * @param Order $order
     * @param MerchantApp $app
     * @param Channel $channel
     * @return array
     */
    public static function queryOrder(Order $order, MerchantApp $app, Channel $channel): array
    {
        $className = self::$channels[$channel->code] ?? '';
        if ($className == '') {
            return self::jsonErr('Error channel code');
        }

        $getResult = function () use ($app, $order) {
            $signStr = sprintf('app_id=%d&merchant_id=%d&order_number=%s&status=%d&key=%s',
                $app->id, $app->merchant_id, $order->order_number, $order->state, $app->key
            );
            $sign = strtoupper(md5($signStr));
            return [
                'app_id' => $app->id,
                'merchant_id' => $app->merchant_id,
                'order_number' => $order->order_number,
                'time' => time(),
                'status' => $order->state,
                'sign' => $sign,
            ];
        };

        // ----------------------------------- 测试补单功能 ------------------------------------
        $result = $className::orderQuery($order->trade_number, $order); // 外部订单号码
        if (!isset($result['orderNum'])) { // 如果不包含订单号
            return self::jsonResult($getResult());
        }

        if (!isset($result['success'])) { //
            return self::jsonResult($getResult());
        }

        $success = $result['success'];
        if (!$success && $order->state == 1) { // 三方失败 - 本地正常 -> 修改本地转为失败
            $order->state = 2;
            $order->remark = '本地成功/三方失败-本地转为失败';
            $order->save();
        }
        if ($success && $order->state != 1) { // 三方成功 - 本地失败 -> 修改本地转为成功
            $order->state = 1;
            $order->remark = '本地失败/三方成功-本地转为成功';
            $order->save();
        }
        $orderStatus = $success ? 1 : 2;
        return $getResult($orderStatus);
    }

    /**
     * 查询订单
     * @param Order $order
     * @param MerchantApp $app
     * @param Channel $channel
     * @return array
     */
    public static function queryOrderV2(Order $order, MerchantApp $app, Channel $channel): array
    {
        $className = self::$channels[$channel->code] ?? '';
        if ($className == '') {
            return self::jsonErr('Error channel code');
        }

        $getResult = function () use ($app, $order) {
            $signStr = sprintf('amount=%d&app_id=%d&merchant_id=%d&order_number=%s&status=%d&key=%s',
                intval($order->amount_paid), $app->id, $app->merchant_id, $order->order_number, $order->state, $app->key
            );
            $sign = strtoupper(md5($signStr));
            return [
                'amount' => intval($order->amount_paid),
                'app_id' => $app->id,
                'merchant_id' => $app->merchant_id,
                'order_number' => $order->order_number,
                'time' => time(),
                'status' => $order->state,
                'sign' => $sign,
            ];
        };

        // ----------------------------------- 测试补单功能 ------------------------------------
        $result = $className::orderQuery($order->trade_number, $order); // 外部订单号码
        if (!isset($result['orderNum'])) { // 如果不包含订单号
            return self::jsonResult($getResult());
        }

        if (!isset($result['success'])) { //
            return self::jsonResult($getResult());
        }

        $success = $result['success'];
        if (!$success && $order->state == Order::STATUS_FINISHED) { // 三方失败 - 本地正常 -> 修改本地转为失败
            $order->state = 2;
            $order->remark = '本地成功 ⇨ 三方失败 ⇨ 本地转为失败';
            $order->save();
        }
        if ($success && $order->state != Order::STATUS_FINISHED) { // 三方成功 - 本地失败 -> 修改本地转为成功
            $order->state = 1;
            $order->remark = '本地失败 ⇨ 三方成功 ⇨ 本地转为成功';
            $order->save();
        }
        return $getResult();
    }

    /**
     * @param string $tradeNumber
     * @param Order|null $order
     * @return array
     */
    public static function orderQuery(string $tradeNumber, Order $order = null): array
    {
        return self::jsonErr('请自行实现 orderQuery');
    }

    /**
     * @param MerchantApp $app
     * @param array $postedData
     * @return array
     */
    public static function info(MerchantApp $app, array $postedData): array
    {
        $orderNum = $postedData['order_number'];
        $row = Order::query()->where('order_number', $orderNum)->first(); // 获取订单信息
        if (!$row) {
            return self::jsonErr('缺少订单号码');
        }
        $statusTypes = [
            '0' => '待付',
            '1' => '成功',
            '2' => '失败',
            '3' => '取消',
            '4' => '拒绝',
            '9' => '其他',
        ];
        return self::jsonResult([
            'order_no' => $orderNum,
            'trade_number' => $row->trade_number,
            'amount' => floatval($row->amount),
            'amount_paid' => floatval($row->amount_paid),
            'created' => $row->created->format('Y-m-d H:i:s'), // 订单创建时间
            'status' => $statusTypes[$row->state] ?? '未知',
            'notify_count' => $row->downstream_notify_count, // 通知次数
            'notify_last' => date('Y-m-d H:i:s', $row->downstream_notified), // 最后通知时间
        ]);
    }

    /**
     * 查询余额
     */
    public static function balance(array $params): array
    {
        // 检测支付通道是否存在
        $channel = $params['channel'];
        if (!isset(self::$channels[$channel])) { // 判断支付渠道是否存在
            return self::jsonErr('指定支付渠道有误');
        }
        $class = self::getChannelClass($channel);
        if (!$class) {
            return self::jsonErr('无法指定支付渠道');
        }

        $result = $class::balance($params);
        return self::jsonArray($result);
    }

    /**
     * 提交出款
     */
    public static function withdraw(array $params): array
    {
        // 检测支付通道是否存在
        $channel = $params['channel'];
        if (!isset(self::$channels[$channel])) { // 判断支付渠道是否存在
            return self::jsonErr('指定支付渠道有误');
        }
        $class = self::getChannelClass($channel);
        if (!$class) {
            return self::jsonErr('无法指定支付渠道');
        }

        $result = $class::withdraw($params);
        return self::jsonArray($result);
    }

    /**
     * 出款查询
     * @param array $params
     * @return array
     */
    public static function withdrawQuery(array $params): array
    {
        // 检测支付通道是否存在
        $channel = $params['channel'];
        if (!isset(self::$channels[$channel])) { // 判断支付渠道是否存在
            return self::jsonErr('指定支付渠道有误');
        }
        $class = self::getChannelClass($channel);
        if (!$class) {
            return self::jsonErr('无法指定支付渠道');
        }

        $result = $class::withdrawQuery($params);
        return self::jsonArray($result);
    }

    /**
     * @return string
     */
    protected static function getNotifyURL(): string
    {
        return trim(config('notify_url'), '/') . '/' . strtolower(static::$channelName);
    }
}
