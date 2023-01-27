<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;

#[Crontab(rule: "*\/10 * * * * *", name: "OrderRates", callback: "execute", memo: "订单费率", enable: true)]
class OrderRates
{

    const FETCH_LIMIT = 10;

    public function setOrderRates()
    {
        $this->execute();
    }

    public function execute()
    {
        // 获取所有费率为0的订单, 每次修改500单
        $orders = Db::table('orders')->where(['rate' => 0])->limit(self::FETCH_LIMIT)->get();
        if (empty($orders) || $orders->isEmpty()) {
            return;
        }

        // 获取此订单对应商户费率, 及平台费率
        $getMerchantIdArr = function () use ($orders): array {
            $idArr = [];
            foreach ($orders as $order) {
                $idArr[$order->merchant_id] = 1;
            }
            return array_keys($idArr);
        };
        $idArr = $getMerchantIdArr();
        if (empty($idArr)) {
            return;
        }

        $merchantRates = Db::select('SELECT * from merchant_payments WHERE merchant_id IN (' . implode(',', $idArr) . ')');
        if (empty($merchantRates)) {  // 如果没有获取到, 则返回
            echo "未找到平台为此商户设定的费率\n";
            return;
        }

        // 得到商户费率
        $channelPayments = Db::table('channel_payments')->get();
        if (empty($channelPayments)) {
            echo "未找到任何支付方式\n";
            return;
        }

        $getPaymentRate = function (int $paymentID) use ($channelPayments): int {
            foreach ($channelPayments as $cp) {
                if ($cp->id == $paymentID) {
                    return $cp->rate;
                }
            }
            return 0;
        };
        $getMerchantRate = function (int $merchantID, int $paymentID) use ($merchantRates): int {
            foreach ($merchantRates as $mr) {
                if ($mr->merchant_id == $merchantID && $mr->payment_id == $paymentID) {
                    return $mr->rate;
                }
            }
            return 0;
        };

        foreach ($orders as $order) {
            if ($order->payment_id == 0 || $order->merchant_id == 0) {
                echo "缺少订单相关商户编号或者支付编号: merchant_id {$order->merchant_id} | payment_id {$order->payment_id} \n";
                continue;
            }

            $rate = $getMerchantRate($order->merchant_id, $order->payment_id);
            $platformRate = $getPaymentRate($order->payment_id);
            $order->rate = $rate; // 商户费率 - 具体至于商户
            $order->platform_rate = $platformRate; // 平台费率 - 最高

            Db::table('orders')->where(['id' => $order->id])->update([
                'rate' => $rate,
                'platform_rate' => $platformRate,
            ]);
        }
    }
}