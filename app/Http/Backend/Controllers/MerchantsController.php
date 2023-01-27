<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Common\Utils;
use App\Constants\Consts;
use App\Model\{AdminLogin, Merchant as ThisModel, MerchantPayment};
use App\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\{Controller, GetMapping, PostMapping, RequestMapping};
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/backend/merchants')]
class MerchantsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    #[GetMapping(path: 'payments')]
    public function merchantPayment(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        $where = [
            'channel_payments.state' => 1, // 支付方式状态
            'channels.state' => 1, // 渠道状态
        ];
        $rows = Db::table('channel_payments')
            ->join('channels', 'channels.id', '=', 'channel_payments.channel_id')
            ->select(['channel_payments.*', 'channels.name as channel_name'])
            ->where($where)
            ->orderBy('channel_payments.channel_id', 'desc')
            ->get();

        $countArr = [];
        $rArr = [];

        $merchantID = intval($request->all()['merchant_id'] ?? '0');
        $mPayments = Db::table('merchant_payments')
            ->where(['merchant_id' => $merchantID])
            ->get();
        // 得到商户费率
        $getMerchantRate = function (int $paymentID) use ($mPayments, $merchantID): int {
            foreach ($mPayments as $r) {
                if ($r->payment_id == $paymentID && $r->merchant_id == $merchantID) {
                    return $r->rate;
                }
            }
            return 0;
        };

        // 得到某个渠道下面支付方式数量
        $getCount = function ($items, int $channelId): int {
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
            $merchantRate = $getMerchantRate($r->id);
            $r->merchant_rate = $merchantRate;
            $r->rate_profit = $merchantRate - $r->rate;
            $r->has_profit = $merchantRate > $r->rate; // 有没利润
            $rArr[] = $r;
        }

        return self::render($request, $render, [
            'rows' => $rArr,
            'paymentTypes' => Consts::PaymentTypes,
            'merchantID' => $merchantID,
        ]);
    }

    #[PostMapping(path: 'payment_save')]
    public function paymentSave(RequestInterface $request): array
    {
        $data = $request->all();
        $merchantID = intval($data['merchant_id'] ?? '0');
        $paymentID = intval($data['payment_id'] ?? '0');
        $rate = intval($data['rate'] ?? '0');
        if ($paymentID <= 0 || $merchantID <= 0 || $rate <= 0) {  //
            return self::jsonErr('渠道或者商户编号有误');
        }

        // 查找之前的支付方式信息
        $where = [
            'merchant_id' => $merchantID,
            'payment_id' => $paymentID,
        ];
        $row = MerchantPayment::query()->where($where)->first();
        $time = time();
        // 保存日志
        $saveLog = function () use ($merchantID, $paymentID, $row, $rate) {
            $saveRecord = [
                'merchant_id' => $merchantID,
                'payment_id' => $paymentID,
                'rate' => $rate,
                'rate_before' => $row ? $row->rate : $rate,
                'created' => time(),
            ];
            Db::table('merchant_payment_logs')->insert($saveRecord);
        };
        if (!$row) {  // 如果不存在, 则添加
            $record = [
                'merchant_id' => $merchantID,
                'payment_id' => $paymentID,
                'created' => $time,
                'updated' => $time,
                'rate' => $rate,
            ];
            if (!Db::table('merchant_payments')->insert($record)) {
                return self::jsonErr('保存商户支付配置信息失败');
            }

            $saveLog(); // 记录日志
            return self::jsonOk();
        }

        // 如果存在, 则修改
        $row->updated = $time;
        $row->rate = $rate;
        if (!$row->save()) {
            return self::jsonErr('保存商户支付配置有误');
        }

        $saveLog(); // 记录日志
        return self::jsonOk();
    }

    public function updateAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = []): ResponseInterface
    {
        $id = $request->query('id'); // 记录编号
        if (!$id || !is_numeric($id)) {
            return self::error($request, $render, '编号有误');
        }
        $row = ThisModel::query()->find($id); // 当前记录
        $controller = self::getControllerName($request);
        $viewFile = $controller . '/edit';
        return $this->editAction($request, $render, $model, array_merge([
            'controller' => $controller,
            'r' => $row,
            "merchantTypes" => ThisModel::MERCHANT_TYPES,
            'merchants' => Db::table('merchants')->where(['state' => 1])->get(),
        ], $data), $viewFile);
    }

    /**
     * @param RequestInterface $request
     * @param RenderInterface $render
     * @return ResponseInterface
     */
    #[GetMapping(path: 'password')]
    public function password(RequestInterface $request, RenderInterface $render): ResponseInterface
    {
        $id = $request->getQueryParams()['id'] ?? '0';
        if (!$id || !is_numeric($id)) {
            return self::error($request, $render, '缺少商户编号');
        }
        return self::render($request, $render, [
            'id' => $id,
            'action' => 'password_save'
        ]);
    }

    #[PostMapping(path: 'password_save')]
    public function passwordSave(RequestInterface $request): array
    {
        $postedData = $request->all(); // 要求: id/password/re_password
        $id = $postedData['id'] ?? '';
        if (!$id || !is_numeric($id)) {
            return self::jsonErr('缺少商户编号');
        }

        $password = trim($postedData['password']) ?? '';
        $rePassword = trim($postedData['re_password']) ?? '';
        if ($password == '' || $rePassword == '') {
            return self::jsonErr('输入的密码或重复密码不能为空');
        }
        if ($password != $rePassword) { // 判断是否密码相等
            return self::jsonErr('两次输入的密码不一致');
        }
        $passLength = strlen($password);
        if ($passLength < 6 || $passLength > 20) {
            return self::jsonErr('用记密码必须在6-20位之间');
        }

        $merchant = ThisModel::query()->find($id);
        if (!$merchant) {
            return self::jsonErr('商户信息不存在');
        }

        $merchant->salt = Utils::getSalt();
        $merchant->password = Utils::getRealPassword($password, $merchant->salt);
        $merchant->updated = time();
        if (!$merchant->save()) {
            return self::jsonErr('保存密码失败');
        }

        return self::jsonOK();
    }

    #[RequestMapping(path: 'google_verify', methods: 'get, post')]
    public function googleVerify(RequestInterface $request): array
    {
        $data = $request->all();
        $merchantID = intval($data['id'] ?? '0');
        if ($merchantID <= 0) {
            return self::jsonErr('传入编号错误');
        }

        $merchant = ThisModel::query()->where(['id' => $merchantID])->first();
        if (!$merchant) {
            return self::jsonErr('缺少商户信息');
        }

        $toState = intval($data['to_google_verify'] ?? '0');
        if ($merchant->google_verify == $toState) {
            return self::jsonErr('当前状态有误, 无法修改');
        }

        $merchant->google_verify = $toState;
        $merchant->updated = time();
        if (!$merchant->save()) {
            return self::jsonErr('保存商户信息出错');
        }

        return self::jsonOk();
    }

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['merchant_name'])) {
            $where[] = ['merchant_name', '=', $params['merchant_name']];
        }
        $pager = $data['pager'] ?? ThisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }

    protected function createAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = []): ResponseInterface
    {
        $admin = (object)$this->session->get(AdminLogin::KEY);
        return parent::createAction($request, $render, $model, [
            'admin' => $admin,
            'merchants' => Db::table('merchants')->where(['state' => 1])->get(),
        ]);
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function createBefore(array &$data): bool
    {
        $data = array_map(function ($v) {
            return trim($v);
        }, $data);

        $salt = Utils::getSalt();
        $list = [
            "merchant_code" => 'MER' . date('YmdHis') . mt_rand(10, 99),
            "salt" => $salt,
        ];
        // $data["password"] = md5('A1@#(DsL)(!@#H985K2CBdh26(##a."|' . $data["password"] . md5($salt));
        $password = trim($data['password']);
        $data['password'] = Utils::getRealPassword($password, $salt);

        if ($data['parent_id'] != '0') {
            $merchant = ThisModel::findByID(intval($data['parent_id']));
            if ($merchant) {
                $data['parent_id'] = $merchant->id;
                $data['parent_name'] = $merchant->name;
            }
        }
        $data = array_merge($data, $list);

        return true;
    }

    protected function updateBefore(array &$data): bool
    {
        if ($data['parent_id'] != '0') {
            $merchant = ThisModel::findByID(intval($data['parent_id']));
            if ($merchant) {
                $data['parent_id'] = $merchant->id;
                $data['parent_name'] = $merchant->name;
            }
        } else {
            $data['parent_name'] = '';
        }
        return true;
    }

    /**
     * @param RequestInterface $request
     * @param array $data
     * @param string $action
     * @return void
     */
    protected function saveAfter(RequestInterface $request, array $data, string $action)
    {
        if ($action == "添加") { // 只能添加代理商户
            // 写入默认的商户费率
            $merchant = Db::table("merchants")->where("id", $data["id"])->first();
            $rows = Db::table('channel_payments')->get();
            foreach ($rows as $r) {
                $time = time();
                $data = [
                    'merchant_id' => $merchant->id,
                    'payment_id' => $r->id,
                    'rate' => $r->rate,
                    'created' => $time,
                    'updated' => $time,
                ];
                Db::table('merchant_payments')->insert($data);
            }
            // $merchants = Db::table("merchants")->where("id", $data["id"])->first();
            // $list = Db::table("merchant_rate")->where("merchant_id", $merchants->parent_id)->get();
            // foreach ($list as $v) {
            //     Db::insert("insert into merchant_rate(merchant_id,type,channel_id,base_rate,lower_rate,merchant_name,parent_id,parent_rate,platform_rate) values(?,?,?,?,?,?,?,?,?)", [
            //         $data["id"],
            //         $v->type,
            //         $v->channel_id,
            //         $v->lower_rate,
            //         $v->lower_rate,
            //         $data["merchant_name"],
            //         $v->merchant_id,
            //         (float)$v->lower_rate - (float)$v->base_rate,
            //         $v->platform_rate,
            //     ]);
            // }
        }
    }
}
