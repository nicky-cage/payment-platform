<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Model;
use App\Model\Channel as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller(prefix: '/backend/channels')]
class ChannelsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', '=', $params['name']];
        }
        if (!empty($params['code'])) {
            $where[] = ['code', '=', $params['code']];
        }
        if (!empty($params['remark'])) {
            $where[] = ['remark', 'like', '%' . $params['remark'] . '%'];
        }
        $pager = $data['pager'] ?? ThisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }
}
