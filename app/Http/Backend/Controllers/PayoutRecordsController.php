<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Model;
use App\Model\{PayoutRecord as ThisModel, Merchant, MerchantApp};
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/payout_records')]
class PayoutRecordsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $where[] = ['state', '>', '0'];
        $pager = $data['pager'] ?? ThisModel::query()->where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants' => Merchant::getRelatedAll(),
            'merchantApps' => MerchantApp::getRelatedAll(),
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }
}