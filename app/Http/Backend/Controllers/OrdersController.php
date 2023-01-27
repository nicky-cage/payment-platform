<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\{ChannelPayment, Model};
use App\Model\MerchantApp;
use App\Model\Order as ThisModel;
use Hyperf\HttpServer\Annotation\{Controller, RequestMapping};
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/backend/orders')]
class OrdersController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    #[RequestMapping(path: 'success', methods: 'get, post')]
    public function success(RequestInterface $request): array
    {
        $orderID = $request->all()['order_id'] ?? 0; // 订单编号
        if ($orderID <= 0) {
            return self::jsonErr('订单编号有误');
        }
        $order = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$order) {  // 如果不存在订单
            return self::jsonErr('订单记录并不存在');
        }

        if ($order->isFinished()) {
            return self::jsonErr('订单已经是成功状态, 无需修改');
        }

        $currentTime = time();
        $order->state = ThisModel::STATUS_FINISHED;
        $order->amount_paid = $order->amount;
        $order->updated = $currentTime;
        $order->finished = $currentTime;
        $order->upstream_confirmed = $currentTime;
        if (!$order->save()) {
            return self::jsonErr('保存订单有误');
        }

        return self::jsonOk();
    }

    // 手工回调
    #[RequestMapping(path: 'failure', methods: 'get, post')]
    public function failure(RequestInterface $request): array
    {
        $orderID = $request->all()['order_id'] ?? 0; // 订单编号
        if ($orderID <= 0) {
            return self::jsonErr('订单编号有误');
        }
        $order = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$order) {  // 如果不存在订单
            return self::jsonErr('订单记录并不存在');
        }

        if ($order->state == ThisModel::STATUS_FAILURE) {
            return self::jsonErr('订单状态异常');
        }

        $currentTime = time();
        $order->state = ThisModel::STATUS_FAILURE;
        $order->amount_paid = 0; // $order->amount;
        $order->updated = $currentTime;
        $order->finished = $currentTime;
        if (!$order->save()) {
            return self::jsonErr('保存订单有误');
        }

        return self::jsonOk();
    }

    // 手工回调
    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['order_number'])) {
            $where[] = ['order_number', '=', $params['order_number']];
        }
        if (!empty($params['trade_number'])) {
            $where[] = ['trade_number', '=', $params['trade_number']];
        }
        $pager = $data['pager'] ?? ThisModel::query()
                ->join("channels", 'orders.channel_id', '=', 'channels.id')
                ->join('merchants', 'orders.merchant_id', '=', 'merchants.id')
                ->select([
                    'orders.*',
                    'channels.name as channel_name',
                    'merchants.merchant_name as merchant_name',
                ])
                ->where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页

        $merchantApps = MerchantApp::getRelatedAll();
        $payments = ChannelPayment::getRelatedAll();

        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchantApps' => $merchantApps,
            'channelPayments' => $payments,
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }
}
