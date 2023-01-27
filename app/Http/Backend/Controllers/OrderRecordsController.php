<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Common\{HttpClient, Utils};
use App\Http\Frontend\Common\BasePay;
use App\Model\{Channel, Merchant, MerchantApp, Model, Order as ThisModel};
use Hyperf\HttpServer\Annotation\{Controller, RequestMapping};
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/backend/order_records')]
class OrderRecordsController extends BaseController
{

    protected static string $modelName = ThisModel::class;

    #[RequestMapping(path: 'notify', methods: 'get, post')]
    public function notify(RequestInterface $request): array
    {
        $orderID = $request->all()['order_id'] ?? 0; // 订单编号
        if ($orderID <= 0) {
            return self::jsonErr('订单编号有误');
        }
        $order = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$order) {  // 如果不存在订单
            return self::jsonErr('订单记录并不存在');
        }

        $app = MerchantApp::query()->where(['id' => $order->app_id])->first();
        if (!$app) {
            return self::jsonErr('缺少应用相关信息');
        }

        $notifyURL = $order->notify_url; // 异步通知结果
        echo "下发地址[order]: $notifyURL \n";
        if (trim($notifyURL) == '') {
            $notifyURL = $app->notify_url;
        }
        if (trim($notifyURL) == '') {
            return self::jsonErr('缺少异步通知地址');
        }

        $isFinished = $order->isFinished();
        if (!$isFinished) {
            return self::jsonErr('未成功订单无法下发, 请先修改订单状态为成功');
        }

        // 一般只有成功才会下发
        echo "下发地址[app]: $notifyURL \n";
        $data = [
            'amount' => $isFinished ? $order->amount_paid : 0,
            'order_number' => $order->order_number,
            'trade_number' => $order->trade_number,
            'status' => $order->status,
            'time' => time(),
        ];

        $sign = Utils::getSign($data, $app->app_key);
        $data['sign'] = $sign;
        print_r(['data' => $data, 'key' => $app->app_key]);

        try {
            $client = HttpClient::getClient();
            $content = trim($client->post($notifyURL, $data));
            if ($content != 'success') {  // 订单成功
                return self::jsonErr('通知下发错误:' . $content);
            }
            return self::jsonOk();
        } catch (\Exception $err) {
            return self::jsonErr('返回结果错误:' . $err->getMessage());
        }
    }

    // 手工回调

    #[RequestMapping(path: 'query_order', methods: 'get, post')]
    public function queryOrder(RequestInterface $request): array
    {
        $orderID = $request->all()['order_id'] ?? 0; // 订单编号
        if ($orderID <= 0) {
            return self::jsonErr('订单编号有误');
        }
        $order = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$order) {  // 如果不存在订单
            return self::jsonErr('订单记录并不存在');
        }

        $app = MerchantApp::query()->where(['id' => $order->app_id])->first();
        if (!$app) {
            return self::jsonErr('缺少应用相关信息');
        }

        $channel = Channel::query()->where(['id' => $order->channel_id])->first();
        if (!$channel) {
            return self::jsonErr('缺少渠道信息');
        }

        $result = BasePay::queryOrder($order, $app, $channel);
        print_r($result);

        return [];
    }

    // 手工回调

    #[RequestMapping(path: 'fix_order', methods: 'get, post')]
    public function fixOrder(RequestInterface $request): array
    {
        $orderID = $request->all()['order_id'] ?? 0; // 订单编号
        if ($orderID <= 0) {
            return self::jsonErr('订单编号有误');
        }
        $order = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$order) {  // 如果不存在订单
            return self::jsonErr('订单记录并不存在');
        }

        $app = MerchantApp::query()->where(['id' => $order->app_id])->first();
        if (!$app) {
            return self::jsonErr('缺少应用相关信息');
        }

        $channel = Channel::query()->where(['id' => $order->channel_id])->first();
        if (!$channel) {
            return self::jsonErr('缺少渠道信息');
        }

        $result = BasePay::queryOrder($order, $app, $channel);
        print_r($result);

        return [];
    }

    // 手工回调

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['trade_number'])) {
            $where[] = ['trade_number', '=', $params['trade_number']];
        }
        $pager = $data['pager'] ?? ThisModel::query()
                ->join("channels", 'orders.channel_id', '=', 'channels.id')
                ->select([
                    'orders.*',
                    'channels.name as channel_name',
                ])
                ->where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants' => Merchant::getRelatedAll(),
            'merchantApps' => MerchantApp::getRelatedAll(),
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }

}
