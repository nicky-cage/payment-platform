<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Model;
use App\Model\MerchantApp as ThisModel;
use App\Model\Merchant;
use Hyperf\View\RenderInterface;
use Hyperf\Utils\Str;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\{GetMapping, RequestMapping};
use Hyperf\DbConnection\Db;

#[Controller(prefix: '/backend/merchant_apps')]
class MerchantAppsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    /**
     * @return array
     */
    #[RequestMapping(path: 'secret', methods: 'get, post')]
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
        $pager = $data['pager'] ?? ThisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        foreach ($pager as $v) {
            $temp = Db::table("merchants")->where("id", $v->merchant_id)->select("name")->first();
            $v->merchant_name = $temp->name;
        }
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
            'merchants' => Merchant::getRelatedAll(),
        ]);
    }

    protected function editAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], string $viewFile = ''): ResponseInterface
    {
        return parent::editAction($request, $render, $model, array_merge($data, [
            'merchants' => Merchant::getRelatedAll()
        ]));
    }
}
