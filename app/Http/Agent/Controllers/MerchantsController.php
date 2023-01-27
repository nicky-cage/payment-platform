<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\Merchant as ThisModel;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Contract\SessionInterface;
use Hyperf\DbConnection\Db;
use App\Common\Utils;

#[Controller(prefix: '/agent/merchants')]
class MerchantsController extends BaseController
{
    protected static string $modelName = ThisModel::class;


    /**
     * @param RenderInterface $render
     * @param $data
     * @return mixed
     */
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

    /**
     * @return array
     */
    #[PostMapping(path: 'password_save')]
    public function passwordSave(RequestInterface $request): array
    {
        $postedData = $request->all(); // 要求: id/password/re_password
        $id = $postedData['id'] ?? '';
        if (!$id || !is_numeric($id)) {
            return self::jsonErr('缺少商户编号');
        }

        $password = $postedData['password'] ?? '';
        $rePassword = $postedData['re_password'] ?? '';
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
        // $merchant->password = md5('A1@#(DsL)(!@#H985K2CBdh26(##a."|' . $password . md5($merchant->salt));
        $merchant->updated = time();
        if (!$merchant->save()) {
            return self::jsonErr('保存密码失败');
        }

        return self::jsonOK();
    }

    #[GetMapping(path: 'lower')]
    public function getLower(RequestInterface $request, RenderInterface $render)
    {

        $params = $request->all();
        $temp = Db::table("merchants")->where("parent_id", $params["id"])->orderBy('id', 'desc')->paginate(15);
        $viewFile = 'merchants/lower';
        $viewData = [
            "rows" => $temp,
        ];
        return self::render($request, $render, $viewData, $viewFile);

    }

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {

        $params = $request->all();
        $where = [];
        if (!empty($params['merchant_name'])) {
            $where[] = ['merchant_name', '=', $params['merchant_name']];
        }
        $temp = $this->session->get("admininfo");

        array_push($where, ["parent_id", "=", $temp["id"]]);
        //获取当前商户,
        // 路径。从这个商户开始。、
        $pager = ThisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }

    protected function getSessionAdminId(SessionInterface $session)
    {
        $adminInfo = $session->get('admininfo');
        return $adminInfo["id"];
    }
}