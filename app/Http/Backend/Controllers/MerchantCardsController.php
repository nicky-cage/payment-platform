<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Model;
use App\Model\MerchantCard as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: "/backend/merchant_cards")]
class MerchantCardsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['card_number'])) {
            $where[] = ['cards.card_number', '=', $params['card_number']];
        }
        $pager = $data['pager'] ?? ThisModel::query()
            ->join("cards", 'merchant_cards.card_id', '=', 'cards.id')
            ->join("merchants", 'merchant_cards.merchant_id', '=', 'merchants.id')
            ->select([
                'merchant_cards.*',
                'cards.card_number',
                'merchants.merchant_name',
            ])
            ->where($where)
            ->orderBy("merchant_cards.id", "desc")->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'statusTypes' => ThisModel::STATUS_TYPES,
            'pollingTypes' => ThisModel::POLLING_TYPES,
        ]);
    }
}
