<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Model;
use App\Model\MerchantApp as ThisModel;
use App\Model\Merchant;
use Hyperf\View\RenderInterface;
use Hyperf\Utils\Str;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\DbConnection\Db;

#[Controller(prefix: '/agent/merchant_apps')]
class MerchantAppsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    /**
     * @return array
     */
    #[GetMapping(path: 'secret')]
    public function secret(RequestInterface $request): array
    {
        $postedData = $request->all();
        $id = $postedData['id'] ?? '';
        if (!$id || !is_numeric($id)) {
            return self::jsonErr('缺少商户APP编号');
        }

        $merchantApp = ThisModel::query()->find($id);
        if (!$merchantApp) {
            return self::jsonErr('商户相关应用信息查找错误');
        }

        $secret = Str::random(32);
        $merchantApp->app_key = $secret;
        if (!$merchantApp->save()) { // 如果保存失败
            return self::jsonErr('修改密钥信息失败');
        }

        return self::jsonResult([
            'key' => $secret,
        ]);
    }

    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', '=', $params['name']];
        }

        $temp = $this->session->get("admininfo");
        $merchantId = Db::table("merchants")->where("parent_id", $temp["id"])->select("id")->get();
        $arr = [];
        foreach ($merchantId as $v) {
            array_push($arr, $v->id);
        }
        $pager = Db::table("merchant_apps")->whereIn("merchant_id", $arr)->where($where)->orderBy('id', 'desc')->paginate(15);
        //  = $data['pager'] ?? ThisModel::where($where) // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants' => Merchant::getRelatedAll(),
        ]);
    }

    protected function editAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], string $viewFile = ''): ResponseInterface
    {
        $temp = $this->session->get("admininfo");
        $merchantId = Db::table("merchants")->where("parent_id", $temp["id"])->select("id")->get();
        $arr = [];
        foreach ($merchantId as $v) {
            array_push($arr, $v->id);
        }
        $res = Db::table("merchant_apps")->whereIn("merchant_id", $arr)->get();
        return parent::editAction($request, $render, $model, array_merge($data, [
            'merchants' => $res
        ]));
    }
}
