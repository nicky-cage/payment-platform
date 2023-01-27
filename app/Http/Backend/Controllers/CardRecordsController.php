<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\CardRecord as ThisModel;
use App\Model\Model;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\DbConnection\Db;

#[Controller(prefix: '/backend/card_records')]
class CardRecordsController extends BaseController
{

    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['merchants.merchant_name', '=', $params['merchant_name']];
        }
        if (!empty($params['order_number'])) {
            $where[] = ['card_records.order_number', '=', $params['order_number']];
        }
        if (!empty($params['bank_order_number'])) {
            $where[] = ['card_records.bank_order_number', '=', $params['bank_order_number']];
        }
        $pager = $data['pager'] ?? ThisModel::query()
                ->join("merchants", 'card_records.merchant_id', '=', 'merchants.id')
                ->join("cards", 'card_records.card_id', '=', 'cards.id')
                ->select([
                    'card_records.id',
                    'card_records.order_number',
                    'card_records.merchant_id',
                    'merchants.merchant_name',
                    'cards.card_number',
                    'card_records.payer_name',
                    'card_records.bank_order_number',
                    'card_records.paid_amount',
                    'card_records.state',
                    'card_records.finished',
                ])
                ->where($where)->orderBy("card_records.id", "desc")->paginate(15); // 如果已存在, 则使用已有分页

        foreach ($pager as $v) {
            $temp = Db::table("merchants")->where("id", $v->merchant_id)->select("name")->first();
            $v->merchant_account = $temp->name;
        }
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }
}