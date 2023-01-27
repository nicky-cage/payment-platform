<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\AdminLoginLog as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/admin_login_logs')]
class AdminLoginLogsController extends BaseController
{
    /**
     * @var string
     */
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['merchant_name'])) {
            $where[] = ['merchants.merchant_name', '=', $params['merchant_name']];
        }
        $pager = $data['pager'] ??  ThisModel::query()
            ->join('merchants', 'admin_login_logs.merchant_id', '=', 'merchants.id')
            ->select(['admin_login_logs.*', 'merchants.merchant_name'])
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate(15); // 如果已存在, 则使用已有分页

        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }
}
