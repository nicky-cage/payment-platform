<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\{Payout as ThisModel, Bank, Merchant, MerchantApp, MerchantLogin};
use App\Model\Model;
use App\Common\{Utils, GoogleCode};
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\{Controller, RequestMapping};

#[Controller(prefix: '/agent/payouts')]
class PayoutsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $where[] = ['state', '=', '0'];
        $pager = $data['pager'] ?? ThisModel::query()->where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants' => Merchant::getRelatedAll(),
            'merchantApps' => MerchantApp::getRelatedAll(),
            'statusTypes' => ThisModel::STATUS_TYPES,
        ]);
    }

    public function createAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = []): ResponseInterface
    {
        return parent::createAction($request, $render, $model, [
            'banks' => Bank::all(),
            'ip' => Utils::clientIP($request),
        ]);
    }

    protected function saveAction(RequestInterface $request, Model $model): array
    {
        $login = MerchantLogin::get($this->session);
        if (!$login) {
            return self::jsonErr('登录信息有误');
        }

        $merchant = Merchant::findByID($login->id);
        if (!$merchant) {
            return self::jsonErr('查找用户信息失败');
        }
        if ($merchant->google_verify == 1 && !GoogleCode::verifyCode($merchant->google_secret, $request['google_code'] ?? '')) {
            return self::jsonErr('谷验验证失败');
        }

        return parent::saveAction($request, $model);
    }

    public function createBefore(array &$data): bool
    {
        $bank = Bank::query()->where(['code' => $data['bank_code']])->first();
        if (!$bank) {
            return false;
        }

        $data['order_number'] = Utils::getOrderNumber('PD');
        $data['trade_number'] = Utils::getOrderNumber('PU');
        $data['bank_name'] = $bank->name;
        return true;
    }

    #[RequestMapping(path: 'cancel', methods: 'get, post')]
    public function cancelOrder(RequestInterface $request): array
    {
        $orderID = $request->all()['order_id'] ?? '';
        if (!$orderID || !is_numeric($orderID)) {
            return self::jsonErr('订单编号有误');
        }

        $row = ThisModel::query()->where(['id' => $orderID])->first();
        if (!$row) {
            return self::jsonErr('缺少订单信息');
        }

        $row->state = ThisModel::STATUS_CANCEL;
        $row->updated = time();
        if (!$row->save()) {
            return self::jsonErr('保存订单信息失败');
        }

        return self::jsonOk();
    }
}
