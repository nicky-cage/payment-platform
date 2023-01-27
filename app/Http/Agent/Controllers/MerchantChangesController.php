<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\MerchantChange as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Contract\PaginatorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/merchant_changes')]
class MerchantChangesController extends BaseController
{

    protected static string $modelName = ThisModel::class;

    /**
     * @param RenderInterface $render
     * @param array $data
     * @param PaginatorInterface|null $pager
     * @return ResponseInterface
     */
    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['merchant_id'])) {
            $where[] = ['merchant_changes.merchant_id', '=', $params['merchant_id']];
        }
        if (!empty($params['merchant_name'])) {
            $where[] = ['merchants.merchant_name', '=', $params['merchant_name']];
        }
        $pager = $data['pager'] ?? ThisModel::query()
            ->join("merchants", 'merchant_changes.merchant_id', '=', 'merchants.id')
            ->select([
                'merchant_changes.*',
                'merchants.merchant_name'
            ])
            ->where($where)->orderBy("id", "desc")->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'changeTypes' => ThisModel::CHANGE_TYPES,
            'pager' => $pager,
        ]);
    }
}
