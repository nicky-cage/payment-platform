<?php

declare(strict_types=1);

namespace App\Http\Frontend\Controllers;

use App\Common\Utils;
use App\Http\Frontend\Common\BasePay;
use App\Model\{Channel, ChannelPayment, MerchantApp, Order};
use Hyperf\HttpServer\Annotation\{Controller, GetMapping, RequestMapping};
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "/payments")]
class PaymentsController extends BaseController
{

    const RETURN_TYPE_JSON = 1;
    const RETURN_TYPE_CONTENT = 2;

    /**
     * 仅限http访问的渠道
     * @var array|string[]
     */
    private static array $httpChannels = [];

    /**
     * 异步回调地址
     * @param RequestInterface $request
     * @return string
     */
    #[RequestMapping(path: "notify", methods: "get, post")]
    public function notify(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - 金星支付

    private static function notifyAll(RequestInterface $request): string
    {
        $path = $request->getUri()->getPath(); // 依据请求url来判断哪个支付方式
        $urls = explode('/', trim($path, '/')); // 拆分url
        if (count($urls) != 3) { // url方式必须正确
            return 'Error URL';
        }

        $data = $request->all();
        print_r($data);

        $channelName = strtoupper($urls[2]); // 将方法字母由小写转换为大写, 以便于获取相关类信息
        $requestIP = Utils::clientIP($request);
        $requestData = array_merge($data, [ // 获取请求的所有参数
            'channel' => $channelName, // 附加请求的类方法
            'request_url' => $request->url(), // 请求url
            'request_ip' => $requestIP,
        ]);
        // 写相关的日志
        return BasePay::notify($requestData, $request); // 处理回调
        // print_r($result)
    }

    // - 皇家支付

    #[RequestMapping(path: "notify/tjx", methods: "get, post")]
    public function notifyTjx(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - 大圣支付

    #[RequestMapping(path: "notify/phj", methods: "get, post")]
    public function notifyPhj(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - 豪杰支付

    #[RequestMapping(path: "notify/tds", methods: "get, post")]
    public function notifyTds(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - LV支付

    #[RequestMapping(path: "notify/thj", methods: "get, post")]
    public function notifyTHj(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - LV支付

    #[RequestMapping(path: "notify/lv", methods: "get, post")]
    public function notifyLv(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - LVLV支付

    #[RequestMapping(path: "notify/lv2", methods: "get, post")]
    public function notifyLv2(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    // - 通一处理回调问题

    #[RequestMapping(path: "notify/plu", methods: "get, post")]
    public function notifyPLU(RequestInterface $request): string
    {
        return self::notifyAll($request);
    }

    /**
     * 订单状态查询
     * 需求字段: order_number/time/sign/app_id
     * @param RequestInterface $request
     * @return array
     */
    #[RequestMapping(path: "query", methods: "get, post")]
    public function query(RequestInterface $request): array
    {
        $params = $request->all();
        $appId = $params['app_id'] ?? '';
        if (!$appId || !is_numeric($appId)) {
            return self::jsonErr('APP ID 有误');
        }

        $orderNumber = $params['order_number'] ?? '';
        if ($orderNumber == '' || strlen($orderNumber) < 12) {
            return self::jsonErr('订单编号格式有误');
        }

        $time = $params['time'] ?? '';
        if (!$time || !is_numeric($time) || $time < time() - 10) { // 必须要时间字段
            return self::jsonErr('订单时间有误');
        }

        $sign = $params['sign'] ?? '';
        if (!$sign) {
            return self::jsonErr('签名错误');
        }

        // 应用信息
        $app = MerchantApp::query()->find($appId);
        if (!$app) {
            return self::jsonErr('获取应用信息有误');
        }

        // 订单信息
        $order = Order::query()->where('order_number', $orderNumber)->first();
        if (!$order) {
            return self::jsonErr('订单信息不存在');
        }

        // 通过渠道信息拿到渠道编码
        $channel = Channel::query()->where('id', $order->channel_id)->first();
        if (!$channel) {
            return self::jsonErr('渠道信息不存在');
        }

        $result = BasePay::queryOrder($order, $app, $channel);
        return self::jsonResult($result);
    }

    /**
     * 订单状态查询
     * 需求字段: order_number/time/sign/app_id
     * @param RequestInterface $request
     * @return array
     */
    #[RequestMapping(path: "query_v2", methods: "get, post")]
    public function queryV2(RequestInterface $request): array
    {
        $params = $request->all();
        $appId = $params['app_id'] ?? '';
        if (!$appId || !is_numeric($appId)) {
            return self::jsonErr('APP ID 有误');
        }

        $orderNumber = $params['order_number'] ?? '';
        if ($orderNumber == '' || strlen($orderNumber) < 12) {
            return self::jsonErr('订单编号格式有误');
        }

        $time = $params['time'] ?? '';
        if (!$time || !is_numeric($time) || $time < time() - 10) { // 必须要时间字段
            return self::jsonErr('订单时间有误');
        }

        $sign = $params['sign'] ?? '';
        if (!$sign) {
            return self::jsonErr('签名错误');
        }

        // 应用信息
        $app = MerchantApp::query()->find($appId);
        if (!$app) {
            return self::jsonErr('获取应用信息有误');
        }

        // 订单信息
        $order = Order::query()->where('order_number', $orderNumber)->first();
        if (!$order) {
            return self::jsonErr('订单信息不存在');
        }

        // 通过渠道信息拿到渠道编码
        $channel = Channel::query()->where('id', $order->channel_id)->first();
        if (!$channel) {
            return self::jsonErr('渠道信息不存在');
        }

        $result = BasePay::queryOrderV2($order, $app, $channel, 'v2');
        return self::jsonResult($result);
    }


    /**
     * 发起支付
     * 需要字段:
     * channel:         渠道代码
     * app_id           商户编号
     * type:            支付方式
     * amount:          金额
     * order_number:    订单号码
     * sign:            签名
     * time:            当前时间戳
     * desc:            商口描述, 可选
     * format:          接口返回格式, 可选
     * @param RenderInterface $render
     * @param RequestInterface $request
     * @return array
     */
    #[RequestMapping(path: "pay", methods: "get, post")]
    public function pay(RequestInterface $request, RenderInterface $render): array
    {
        // ------------------ 数据检测 ---------------------------------- //
        $postedData = $request->all(); // 提交数据
        print_r($postedData);
        // $resultType = $postedData['result_type'] ?? 1;
        if (!isset($postedData['app_id']) || !is_numeric($postedData['app_id'])) { // 必须提供商户编号
            return self::jsonErr('No App ID.');
        }
        if (!isset($postedData['channel'])) { // 支付渠道
            return self::jsonErr('No Channel.');
        }
        if (!isset($postedData['type'])) { // 支付类型
            return self::jsonErr('No Payment Type.');
        }
        if (!isset($postedData['amount']) || !is_numeric($postedData['amount'])) { // 支付金额
            return self::jsonErr('No Amount.');
        }
        if (!isset($postedData['order_number'])) { // 发起方订单号码
            return self::jsonErr('No Order Number.');
        }
        if (!isset($postedData['sign'])) { // 缺少签名
            return self::jsonErr('No Sign.');
        }
        if (!isset($postedData['time'])) { // 提交时间
            return self::jsonErr('No Time');
        }
        $time = $postedData['time'];
        if (!is_numeric($time) || $time < time() - 300) { // 必须在10秒内处理
            return self::jsonErr('Order Time Out.');
        }

        // $channel = strtoupper($postedData['channel']); // 渠道名称, 如: TAB, 一般3个字符, 大写

        // 检测签名相关
        $appId = $postedData['app_id'];
        $merchantApp = MerchantApp::query()->find($appId);
        if (!$merchantApp) {
            return self::jsonErr('Merchant Error.');
        }

        // ------------------ 数据处理 ---------------------------------- //商戶需不需要對應IP？
        if (!isset($params['client_ip'])) {  // 如果没有设置ip, 则设置ip
            $clientIp = Utils::clientIP($request);
            $postedData['client_ip'] = $clientIp; // 设置来源IP
        }

        $result = (object)BasePay::pay($postedData, $merchantApp); // 如果出现错误, 则直接输出错误信息
        print_r($result);
        if (!isset($result->code) || $result->code != 0) { // 如果有错误
            return self::jsonErr($result->message ?? 'Error: No Result Code');
        }

        $data = (object)$result->data; // 数据结构 content/url/form
        if (!isset($data->url) && !isset($data->content) && !isset($data->form)) {
            return $this->jsonErr($result->message ?? 'Error: No Parameter.');
        }

        return self::jsonObject($data);
        // /**
        //  * 1. 如果是网页内容输出, 则直接输出全部网页内容
        //  */
        // if (isset($data->content) && $data->content != '') { // 如果获取的是内容
        //     return self::render($request, $render, ['content' => $result->data['content']], 'deposits/content');
        // }

        // /**
        //  * 2. 如果需要自定义的渲染页面
        //  */
        // if (isset($data->custom) && isset($data->form)) {
        //     $viewFile = 'deposits/' . strtolower($channel);
        //     return self::render($request, $render, [
        //         'form' => $data->form,
        //     ], $viewFile);
        // }

        // /**
        //  * 3. 如果需要跳转form提交,则渲染 form 页面
        //  */
        // if (isset($data->form) && is_array($data->form)) {
        //     return self::render($request, $render, [
        //         'url' => $data->url, // 要跳转的地址
        //         'form' => $data->form // 要提交的值
        //     ], 'deposits/form');
        // }

        // /**
        //  *  4. URL跳转, 如果获取的是URL则由模板直接跳转到相应的页面
        //  */
        // return self::render($request, $render, [
        //     'url' => $data->url,
        //     'auto_redirect' => ($data->auto_redirect ?? false),
        //     'return_url' => $postedData['return_url'],
        // ], 'deposits/url');
    }

    /**
     * @return array
     */
    #[RequestMapping(path: "channels", methods: "post, get")]
    public function channels(): array
    {
        return ChannelPayment::getPayments();
    }

    /**
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @return array
     */
    #[RequestMapping(path: "info", methods: "get, post")]
    public function info(RequestInterface $request, RenderInterface $render): array
    {
        $postedData = $request->all(); // 提交数据
        if (!isset($postedData['app_id']) || !is_numeric($postedData['app_id'])) { // 必须提供商户编号
            return self::error($request, $render, '缺少商户编号');
        }
        if (!isset($postedData['sign'])) { // 缺少签名
            return self::jsonErr('缺少下单签名');
        }
        if (!isset($postedData['order_number'])) { // 发起方订单号码
            return self::error($request, $render, '缺少订单编号信息');
        }
        if (!isset($postedData['time']) || !is_numeric($postedData['time'])) { // 提交时间
            return self::jsonErr('缺少下单时间信息时间');
        }
        $time = $postedData['time'];
        if (!is_numeric($time) || $time < time() - 300) { // 必须在10秒内处理
            return self::jsonErr('订单时间不对或者下单超时');
        }

        // 检测签名相关
        $appId = $postedData['app_id'];
        $merchantApp = MerchantApp::query()->find($appId);
        if (!$merchantApp) {
            return self::jsonErr('商户相关信息检测失败');
        }

        return BasePay::info($merchantApp, $postedData);
    }

    /**
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @param string $message
     * @return ResponseInterface
     */
    protected static function error(RequestInterface $request, RenderInterface $render, string $message = '程序执行发生错误'): ResponseInterface
    {
        return self::render($request, $render, [
            'message' => $message,
        ], 'error');
    }

    /**
     * 出款
     * 需要字段:
     * amount:          金额
     * order_number:    订单号码
     * sign:            签名
     * time:            当前时间戳
     * bank_name:       银行名称
     * bank_code:       银行代码
     * bank_branch:     银行支行地址
     * bank_card:       银行卡号
     * name:            开户姓名
     * amount:          金额
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @return array
     */
    #[GetMapping(path: "withdraw")]
    public function withdraw(RequestInterface $request, RenderInterface $render): array
    {
        // ------------------ 数据检测 ---------------------------------- //
        $postedData = $request->all(); // 提交数据
        if (!isset($postedData['app_id']) || !is_numeric($postedData['app_id'])) { // 必须提供商户编号
            return self::jsonErr('缺少商户编号');
        }
        if (!isset($postedData['amount']) || !is_numeric($postedData['amount'])) { // 支付金额
            return self::jsonErr('缺少支付金额信息');
        }
        if (!isset($postedData['order_number'])) { // 发起方订单号码
            return self::jsonErr('缺少订单编号信息');
        }
        if (!isset($postedData['sign'])) { // 缺少签名
            return self::jsonErr('缺少下单签名');
        }
        if (!isset($postedData['time'])) { // 提交时间
            return self::jsonErr('缺少下单时间信息时间');
        }
        $time = $postedData['time'];
        if (!is_numeric($time) || $time < time() - 300) { // 必须在10秒内处理
            return self::jsonErr('订单时间不对或者下单超时');
        }

        // 检测签名相关
        $appId = $postedData['app_id'];
        $merchantApp = MerchantApp::query()->find($appId);
        if (!$merchantApp) {
            return self::jsonErr('商户相关信息检测失败');
        }

        // ------------------ 数据处理 ---------------------------------- //
        $clientIP = Utils::clientIP($request);
        $postedData['client_ip'] = $clientIP; // 设置来源IP
        $result = (object)BasePay::withdraw($postedData, $merchantApp); // 如果出现错误, 则直接输出错误信息
        if ($result->code != 0) { // 如果没有错误
            return self::jsonErr($result->message);
        }

        return $result;
    }

    /**
     * 查询账户余额
     * @param RequestInterface $request
     * @return array
     */
    #[GetMapping(path: "balance")]
    public function balance(RequestInterface $request): array
    {
        // ------------------ 数据检测 ---------------------------------- //
        $postedData = $request->all(); // 提交数据
        if (!isset($postedData['app_id']) || !is_numeric($postedData['app_id'])) { // 必须提供商户编号
            return self::jsonErr('缺少商户编号');
        }
        if (!isset($postedData['sign'])) { // 缺少签名
            return self::jsonErr('缺少下单签名');
        }
        if (!isset($postedData['time'])) { // 提交时间
            return self::jsonErr('缺少下单时间信息时间');
        }
        $time = $postedData['time'];
        if (!is_numeric($time) || $time < time() - 300) { // 必须在10秒内处理
            return self::jsonErr('订单时间不对或者下单超时');
        }

        // ------------------ 数据处理 ---------------------------------- //
        $clientIP = Utils::clientIP($request);
        $postedData['client_ip'] = $clientIP; // 设置来源IP
        $result = (object)BasePay::balance($postedData); // 如果出现错误, 则直接输出错误信息
        if ($result->code != 0) { // 如果没有错误
            return self::jsonErr($result->message);
        }

        return $result;
    }

    /**
     * 查询提现订单
     * @param RequestInterface $request
     * @return array
     */
    #[GetMapping(path: "withdraw_query")]
    public function withdrawQuery(RequestInterface $request): array
    {
        // ------------------ 数据检测 ---------------------------------- //
        $postedData = $request->all(); // 提交数据
        if (!isset($postedData['app_id']) || !is_numeric($postedData['app_id'])) { // 必须提供商户编号
            return self::jsonErr('缺少商户编号');
        }
        if (!isset($postedData['order_number'])) { // 发起方订单号码
            return self::jsonErr('缺少订单编号信息');
        }
        if (!isset($postedData['sign'])) { // 缺少签名
            return self::jsonErr('缺少下单签名');
        }
        if (!isset($postedData['time'])) { // 提交时间
            return self::jsonErr('缺少下单时间信息时间');
        }
        $time = $postedData['time'];
        if (!is_numeric($time) || $time < time() - 300) { // 必须在10秒内处理
            return self::jsonErr('订单时间不对或者下单超时');
        }

        // ------------------ 数据处理 ---------------------------------- //
        $clientIP = Utils::clientIP($request);
        $postedData['client_ip'] = $clientIP; // 设置来源IP
        $result = (object)BasePay::withdrawQuery($postedData); // 如果出现错误, 则直接输出错误信息
        if ($result->code != 0) { // 如果没有错误
            return self::jsonErr($result->message);
        }

        return $result;
    }
}
