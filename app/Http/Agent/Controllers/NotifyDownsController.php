<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\Merchant;
use App\Model\MerchantApp;
use App\Model\NotifyDown as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/notify_downs')]
class NotifyDownsController extends BaseController
{

    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['merchant_id'])) {
            $where[] = ['merchant_id', '=', $params['merchant_id']];
        }
        if (!empty($params['app_id'])) {
            $where[] = ['app_id', '=', $params['app_id']];
        }
        $pager = $data['pager'] ?? ThisModel::where($where)->orderBy("id", "desc")->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants' => Merchant::getRelatedAll(),
            'merchantApps' => MerchantApp::getRelatedAll(),
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }
}
