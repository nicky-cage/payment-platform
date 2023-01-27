<?php

declare(strict_types=1);

namespace App\Http\Backend\Controllers;

use App\Model\Card as Model;
use App\Model\Bank;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: '/backend/cards')]
class CardsController extends BaseController
{
    protected static string $modelName = Model::class;

//    protected static array $validations = [
//        'save' => [
//            'rules' => [
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
    protected function getQueryCond(RequestInterface $request): array
    {
        return [
            '%' => 'bank_name, bank_code, card_number',
        ];
    }
}