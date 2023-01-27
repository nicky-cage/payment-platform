<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Order as ThisModel;
use App\Model\Model;
use App\Model\MerchantApp;
use App\Model\Merchant;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/orders')]
class OrdersController extends BaseController
{
    protected static string $modelName = ThisModel::class;

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
