<?php
declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;

class Consts extends AbstractConstants
{
    /**
     * 支付方式分类
     */
    const PaymentTypes = [
        0 => '银行转账 (离线)',
        1 => '在线网银',
        2 => '支付宝支付',
        3 => '微信支付',
        4 => 'QQ 钱包',
        5 => '快捷支付',
        6 => '京东支付',
        7 => '银联扫码',
        8 => '虚拟货币',
        9 => '云闪付',
    ];
}
