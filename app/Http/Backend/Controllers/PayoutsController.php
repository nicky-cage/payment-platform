<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Model;
use App\Model\{Payout as ThisModel, Merchant, MerchantApp};
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\{Controller, RequestMapping};

#[Controller(prefix: '/backend/payouts')]
class PayoutsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $where[] = ['state', '=', '0'];
        $pager = $data['pager'] ?? ThisModel::query()->where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants'=> Merchant::getRelatedAll(),
            'merchantApps' => MerchantApp::getRelatedAll(),
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }

    #[RequestMapping(path: 'success', methods: 'get, post')]
    public function orderSuccess(RequestInterface $request): array { 
        $orderID = $request->all()['order_id'] ?? '';
        if (!$orderID || !is_numeric($orderID)) { 
            return self::jsonErr('订单编号有误');
        }

        $row = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$row) {
            return self::jsonErr('缺少订单信息');
        }

        $currentTime = time();
        $row->state = ThisModel::STATUS_SUCCESS;
        $row->amount_paid = $row->amount;
        $row->updated = $currentTime;
        $row->finished = $currentTime;
        $row->upstream_confirmed = $currentTime;
        if (!$row->save()) { 
            return self::jsonErr('保存订单信息失败');
        }

        return self::jsonOk();
    }

    #[RequestMapping(path: 'deny', methods: 'get, post')]
    public function orderDeny(RequestInterface $request): array { 
        $orderID = $request->all()['order_id'] ?? '';
        if (!$orderID || !is_numeric($orderID)) { 
            return self::jsonErr('订单编号有误');
        }

        $row = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$row) {
            return self::jsonErr('缺少订单信息');
        }

        $row->state = ThisModel::STATUS_DENY;
        $row->updated = time();
        if (!$row->save()) { 
            return self::jsonErr('保存订单信息失败');
        }

        return self::jsonOk();
    }

}