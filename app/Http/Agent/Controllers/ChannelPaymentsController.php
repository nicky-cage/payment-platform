<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\ChannelPayment as ThisModel;
use App\Model\Channel;
use App\Model\Model;
use Hyperf\View\RenderInterface;
use App\Constants\Consts;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\{GetMapping, PostMapping, RequestMapping};

#[Controller(prefix: '/agent/channel_payments')]
class ChannelPaymentsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    public function updateAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = []): ResponseInterface
    {
        $id = $request->query('id'); // 记录编号
        if (!$id || !is_numeric($id)) {
            return self::error($request, $render, '编号有误');
        }

        $row = $model::query()->find($id); // 当前记录
        $admin = $this->session->get("admininfo");
        $merchants = Db::table("merchants")->where("parent_id", $admin["id"])->get();
        $merchantRate = Db::table("merchant_rate")->where("merchant_id", $admin["id"])->where("channel_id", $row->channel_id)->where("type", $row->code)->first();
        if ($merchantRate) {
            $row->rate_lower = $merchantRate->lower_rate;
        } else {
            $row->rate_lower = 0;
        }
        $row->rate = $merchantRate->base_rate;
        $controller = self::getControllerName($request);
        $viewFile = $controller . '/edit';
        return $this->editAction($request, $render, $model, array_merge([
            'controller' => $controller,
            'r' => $row,
            'merchant' => $merchants,
        ], $data), $viewFile);
    }

    protected function editAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], string $viewFile = ''): ResponseInterface
    {
        return parent::editAction($request, $render, $model, array_merge([
            'channels' => Channel::getRelatedAll(),
        ], $data), $viewFile);
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    #[PostMapping(path: 'save')]
    public function save(RequestInterface $request): array
    {
        $data = $request->post();
        $id = $data["id"];
        Db::update("update channel_payments set amount_max=?,amount_min=?,amounts=?,channel_id=?,code=?,name=?,payment_type=?,state=? where id=?", [
            $data["amount_max"],
            $data["amount_min"],
            $data["amounts"],
            $data["channel_id"],
            $data["code"],
            $data["name"],
            $data["payment_type"],
            $data["state"],
            $id
        ]);

        // 关系表
        $admin = $this->session->get("admininfo");
        $merchantsId = 0;
        if ($data["merchant"] != "") {
            $merchants = Db::table("merchants")->where("id", $data["merchant"])->select("id")->first();
            $merchantsId = $merchants->id;
        } else {
            $merchantsId = $admin["id"];
        }

        $merchantRate = Db::table("merchant_rate")->where("merchant_id", $merchantsId)->where("channel_id", $data["channel_id"])->where("type", $data["code"])->first();

        $lowerRate = 0;
        $parentRate = 0;
        if ($merchantRate->base_rate >= $data["rate_lower"]) {
            $lowerRate = $merchantRate->base_rate;
        } else {
            $lowerRate = (float)$data["rate_lower"];
            $parentRate = (float)$data["rate_lower"] - (float)$merchantRate->base_rate;
        }
        if ($data["merchant"] != "") {
            Db::update("update merchant_rate set lower_rate=?, parent_rate=? where channel_id=? and type=? and merchant_id=? ", [$lowerRate, $parentRate, $data["channel_id"], $data["code"], $merchantsId]);

        } else {
            Db::update("update merchant_rate set lower_rate=? where channel_id=? and type=? and merchant_id=?", [$lowerRate, $data["channel_id"], $data["code"], $merchantsId]);

        }
        return self::jsonOK();
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    #[GetMapping(path: 'get_info')]
    public function getInfo(RequestInterface $request): array
    {
        $data = $request->all();
        $merchantRate = Db::table("merchant_rate")->where("merchant_id", $data["id"])->where("channel_id", $data["channel_id"])->where("type", $data["code"])->first();
        return self::jsonOk($merchantRate->lower_rate);
    }

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
            ->select([
                'channel_payments.*',
                'channels.name as channels_name',
            ])
            ->where($where)
            ->orderBy('channel_payments.channel_id', 'desc')
            ->paginate(15);
        //点位
        $admin = $this->session->get("admininfo");
        foreach ($pager as $v) {
            $merchantRate = Db::table("merchant_rate")->where("merchant_id", $admin["id"])->where("channel_id", $v->channel_id)->where("type", $v->code)->first();
            if ($merchantRate) {
                $v->rate_lower = $merchantRate->lower_rate;
            } else {
                $v->rate_lower = 0;
            }
        }
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
}