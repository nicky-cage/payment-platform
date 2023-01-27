<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Model;
use App\Model\NotifyUp as ThisModel;
use Hyperf\View\RenderInterface;
use Ip2Region;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/notify_ups')]
class NotifyUpsController extends BaseController
{
    /**
     * @var Ip2Region
     */
    protected Ip2Region $ip2region;

    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['merchant_apps.name', '=', $params['name']];
        }
        $pager = $data['pager'] ?? ThisModel::query()
            ->join("merchants", 'notify_ups.merchant_id', '=', 'merchants.id')
            ->join('merchant_apps', 'notify_ups.app_id', '=', 'merchant_apps.id')
            ->join('channels', 'notify_ups.channel_id', '=', 'channels.id')
            ->where($where)
            ->select([
                'notify_ups.*',
                'merchant_apps.name',
                'channels.name as channel_name',
            ])
            ->orderBy("id", "desc")->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }
}
