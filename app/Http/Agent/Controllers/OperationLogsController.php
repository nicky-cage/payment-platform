<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\OperationLog as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/operation_logs')]
class OperationLogsController extends BaseController
{

    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $where = [];
        $params = $request->getQueryParams();
        if (isset($params['name']) && $params['name'] != '') {
            $where = ['admins.name' => $params['name']];
        }
        $pager = ThisModel::query()->join("admins", 'operation_logs.admin_id', '=', 'admins.id')
            ->where($where)->orderBy('operation_logs.id', 'desc')->select(['operation_logs.*', 'admins.name'])->paginate(15);
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }
}
