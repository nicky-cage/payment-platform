<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\ChannelDownStream as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/channel_down_streams')]
class ChannelDownStreamsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['up_stream_name'])) {
            $where[] = ['up_stream_name', '=', $params['up_stream_name']];
        }
        $pager = $data['pager'] ?? ThisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页

        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }
}
