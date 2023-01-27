<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Constants\Consts;
use App\Model\Channel;
use App\Model\ChannelPayment as ThisModel;
use App\Model\Model;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/backend/channel_payments')]
class ChannelPaymentsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['channels_name'])) {
            $where[] = ['channels.name', '=', $params['channels_name']];
        }
        if (!empty($params['channel_id'])) {
            $where[] = ['channel_payments.channel_id', '=', $params['channel_id']];
        }
        $pager = ThisModel::query()
            ->join("channels", 'channel_payments.channel_id', '=', 'channels.id')
            ->select(['channel_payments.*', 'channels.name as channels_name',])
            ->where($where)
            ->orderBy('channel_payments.channel_id', 'desc')
            ->paginate(15);
        $rows = $pager->items();
        $countArr = [];
        $rArr = [];
        // 得到某个渠道下面支付方式数量
        $getCount = function ($items, $channelId): int {
            $count = 0;
            foreach ($items as $r) {
                if ($r->channel_id == $channelId) {
                    $count += 1;
                }
            }
            return $count;
        };
        foreach ($rows as $r) {
            if (isset($countArr[$r->channel_id])) {
                $r->pay_count = 0;
            } else {
                $r->pay_count = $getCount($rows, $r->channel_id);
                $countArr[$r->channel_id] = $r->pay_count;
            }
            $rArr[] = $r;
        }

        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'statusTypes' => ThisModel::STATUS_TYPES,
            'paymentTypes' => Consts::PaymentTypes
        ]);
    }

    protected function editAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], string $viewFile = ''): ResponseInterface
    {
        return parent::editAction($request, $render, $model, array_merge([
            'channels' => Channel::getRelatedAll(),
        ], $data), $viewFile);
    }

    // protected function saveAfter(RequestInterface $request, array $data, string $action)
    // {
    //     // $temp = (float)$data["rate"] + (float)$data["rate_platform"];
    //     // DB::update("update channel_payments set rate_lower=? where id=?", [$temp, $data["id"]]);
    //     // $channel = Db::table("merchant_rate")->where("channel_id", $data["channel_id"])->where("type", $data["code"])->first();
    //     // if (!$channel) {
    //     //     $merchants = Db::table("merchants")->select("id", "name", "parent_id", "parent_name")->get();
    //     //     foreach ($merchants as $v) {
    //     //         $parentRate = 0;
    //     //         if ($v->parent_id != "0") {
    //     //             $parentRate = $temp;
    //     //         }
    //     //         Db::insert("insert into merchant_rate(merchant_id,type,channel_id,base_rate,lower_rate,merchant_name,parent_id,parent_rate,platform_rate) values(?,?,?,?,?,?,?,?,?)", [
    //     //             $v->id,
    //     //             $data["code"],
    //     //             $data["channel_id"],
    //     //             $temp,
    //     //             $temp,
    //     //             $v->name,
    //     //             $v->parent_id ?? '',
    //     //             $parentRate,
    //     //             $data["rate_platform"],
    //     //         ]);
    //     //     }
    //     //     return;
    //     // }
    //     // Db::update("update merchant_rate set base_rate=? where channel_id=? and type=?", [$temp, $data["channel_id"], $data["code"]]);
    // }
}