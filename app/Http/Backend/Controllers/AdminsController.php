<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Common\Utils;
use App\Model\Model;
use App\Model\Admin;
use App\Model\Admin as ThisModel;
use App\Model\AdminRole;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/admins')]
class AdminsController extends BaseController
{
    protected static string $modelName = ThisModel::class;

    protected function getQueryCond(RequestInterface $request): array
    {
        return [
            '%' => 'name',
            '=' => 'state',
        ];
    }

    public function createAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = []): ResponseInterface
    {
        return parent::createAction($request, $render, $model, [
            'adminRoles' => AdminRole::getRelatedAll(),
        ]);
    }

    public function updateAction(RequestInterface $request, RenderInterface $render,  Model $model, array $data = []): ResponseInterface
    {
        return parent::updateAction($request, $render, $model, [
            'adminRoles' => AdminRole::getRelatedAll(),
        ]);
    }

    public function saveAction(RequestInterface $request, Model $model): array
    {
        $params = array_map(function ($v) {
            return trim($v);
        }, $request->all());
        $isCreate = true;
        if (isset($params['id'])) {
            $id = intval($params['id']);
            if ($id == 0) {
                unset($params['id']);
            } else {
                $isCreate = false;
            }
        }
        $role = AdminRole::query()->where(['id' => $params['role_id']])->first();
        $params['role_name'] = $role->name;
        $params['google_verify'] = 0;
        if ($isCreate) {
            $name = trim($params['name']);
            $row = Admin::query()->where(['name' => $name])->first();
            if ($row) {
                return self::jsonErr('添加失败: 后台用户名称已经存在');
            }
            $currentTime = time();
            $params['salt'] = Utils::getSalt();
            $params['password'] = Utils::getRealPassword($params['password'], $params['salt']);
            $params['created'] = $currentTime;
            $params['updated'] = $currentTime;
            Admin::query()->insert($params);
        } else {
            $id = $params['id'];
            unset($params['id']);
            $params['updated'] = time();
            Admin::query()->where(['id' => $id])->update($params);
        }
        return self::jsonOK();
    }
}
