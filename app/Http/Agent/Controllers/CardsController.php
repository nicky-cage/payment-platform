<?php

declare(strict_types=1);

namespace App\Http\Agent\Controllers;

use App\Model\Card as thisModel;
use App\Model\Bank;
use App\Model\Model;
use Hyperf\View\RenderInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: '/agent/cards')]
class CardsController extends BaseController
{
    protected static string $modelName = thisModel::class;
//
//    protected static array $validations = [
//        'save' => [
//            'rule' => [
//                'each_max' => 'required',
//                'each_min' => 'required',
//                'pay_max' => 'required',
//                'call_count' => 'required',
//            ],
//            'messages' => [
//                'each_max.required' => '必须输入单笔最高',
//                'each_min.required' => '必须输入单笔最低',
//                'pay_max.required' => '必须输入最高支付额度',
//                'call_count.required' => '必须最多调用次数',
//            ],
//        ]
//    ];

    //
    protected function editAfter(RequestInterface $request, array $data = []): array
    {
        return ['banks' => Bank::getAll()];
    }

    // 
//    protected function getQueryCond(RequestInterface $request): array
//    {
//        return [
//            '%' => 'bank_name, bank_code, card_number',
//        ];
//    }
    protected function listAction(RequestInterface $request, RenderInterface $render, Model $model, array $data = [], array $conditions = []): ResponseInterface
    {
        $params = $request->all();
        $where = [];
        if (!empty($params['bank_name'])) {
            $where[] = ['bank_name', '=', $params['bank_name']];
        }
        if (!empty($params['bank_code'])) {
            $where[] = ['bank_code', '=', $params['bank_code']];
        }
        if (!empty($params['card_number'])) {
            $where[] = ['card_number', '=', $params['card_number']];
        }
        array_push($where, ["status", "=", 1]);
        $pager = $data['pager'] ?? thisModel::where($where)->orderBy('id', 'desc')->paginate(15); // 如果已存在, 则使用已有分页
        return parent::listAction($request, $render, $model, [
            'pager' => $pager,
        ]);
    }

//    protected function getQueryCurrent(RequestInterface $request): array
//    {
//        return ["status", "=", 1];
//    }
}