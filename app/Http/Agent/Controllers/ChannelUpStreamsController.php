<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Channel;
use App\Model\Model;
use App\Model\ChannelUpStream as ThisModel;
use Hyperf\DbConnection\Db;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/agent/channel_up_streams')]
class ChannelUpStreamsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', '=', $params['name']];
        }
        $pager = $data['pager'] ?? ThisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页

        return parent::listAction($request, $render, $model);
    }

    public function createAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = []): ResponseInterface
    {
        return parent::editAction($request, $render, $model, array_merge([
            'channels' => Channel::getRelatedAll()
        ], $data));
    }

    public function updateAction(RequestInterface $request, RenderInterface $render,  Model $model, array $data = []): ResponseInterface
    {
        return parent::editAction($request, $render, $model, array_merge([
            'channels' => Channel::getRelatedAll()
        ], $data));
    }

    /**
     * @return array
     */
    public function saveAction(RequestInterface $request, Model $model): array
    {
        $params = $this->request->all();
        unset($params['card_number']);
        try {
            $row = Channel::query()->where(['code' => $params['code']])->first();
            $params['name'] = $row->name;
            if ($params['id'] == 0) {
                unset($params['id']);
                $params['created'] = strtotime(date("Y-m-d H:i:s"));
                Db::table("channel_up_streams")->insert($params);
            } else {
                $params['updated'] = strtotime(date("Y-m-d H:i:s"));
                Db::table("channel_up_streams")->where(['id' => $params['id']])->update($params);
            }
        } catch (\Throwable $ex) {
            return self::jsonErr('保存数据失败');
        }
        return self::jsonOK();
    }
}
