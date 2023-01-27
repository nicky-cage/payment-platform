<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;

#[Crontab(rule: "*\/20 * * * * *", name: "MerchantAccount", callback: "execute", memo: "商户统计", enable: true)]
class MerchantAccount
{

    const FETCH_LIMIT = 10;

    public function setOrderRates()
    {
        $this->execute();
    }

    public function execute()
    {
        $merchants = DB::table("merchants")->select()->get();
        $getIdArr = function () use ($merchants): array {
            $rArr = [];
            foreach ($merchants as $m) {
                $rArr[] = $m->id;
            }
            return $rArr;
        };
        $mIdStr = implode(',', $getIdArr());
        if ($mIdStr == '') {
            echo "商户数量为零\n";
            return;
        }

        $rArr = [];
        foreach ($merchants as $merchant) {
            $rArr[] = [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'app_count' => 0,
                'order_count' => 0,
                'order_valid' => 0,
                'order_total' => 0,
                'order_total_valid' => 0,
                'order_success' => 0,
                'real_merchant' => 0,
                'real_platform' => 0,
            ];
        }
        $aSQL = "SELECT merchant_id, COUNT(*) AS app_count FROM merchant_apps WHERE merchant_id IN ($mIdStr) GROUP BY merchant_id";
        $aRows = Db::select($aSQL);
        foreach ($aRows as $row) {
            foreach ($rArr as &$r) {
                if ($r['id'] == $row->merchant_id) {
                    $r['app_count'] = $row->app_count;
                }
            }
        }

        $oSQL = "SELECT merchant_id, COUNT(*) AS order_count, SUM(IF(state = 1, 1, 0)) AS order_valid, " .
            "SUM(amount) AS order_total, SUM(IF(state = 1, amount_paid, 0)) AS order_total_valid, " .
            "SUM(IF(state = 1 AND rate > 0, (100 - rate) / 100.0 * amount_paid, 0)) AS real_merchant, " .
            "SUM(IF(state = 1 AND platform_rate > 0, (100 - platform_rate) / 100.0 * amount_paid, 0)) AS real_platform " .
            "FROM orders WHERE merchant_id IN ($mIdStr) GROUP BY merchant_id";
        $oRows = Db::select($oSQL);
        foreach ($oRows as $row) {
            foreach ($rArr as &$r) {
                if ($r['id'] == $row->merchant_id) {
                    $r['order_count'] = $row->order_count;
                    $r['order_valid'] = $row->order_valid;
                    $r['order_total'] = $row->order_total;
                    $r['order_total_valid'] = $row->order_total_valid;
                    if ($row->order_count != 0 && $row->order_valid != 0) {
                        $r['order_success'] = round(floatval($row->order_valid) / floatval($row->order_count) * 100.0, 2);
                    } else {
                        $r['order_success'] = 0;
                    }
                    $r['real_merchant'] = $row->real_merchant;
                    $r['real_platform'] = $row->real_platform;
                }
            }
        }

        foreach ($rArr as $r) {
            $ma = Db::table('merchant_accounts')->where('merchant_id', '=', $r['id'])->first();
            if ($ma) {
                Db::table('merchant_accounts')->where('merchant_id', '=', $r['id'])->update([
                    'app_count' => $r['app_count'],
                    'order_count' => $r['order_count'],
                    'order_valid' => $r['order_valid'],
                    'order_total' => $r['order_total'],
                    'order_total_valid' => $r['order_total_valid'],
                    'order_success' => $r['order_success'],
                    'real_merchant' => $r['real_merchant'],
                    'real_platform' => $r['real_platform'],
                ]);
                continue;
            }

            // 写入
            Db::table('merchant_accounts')->insert([
                'merchant_id' => $r['id'],
                'merchant_name' => $r['name'],
                'app_count' => $r['app_count'],
                'order_count' => $r['order_count'],
                'order_valid' => $r['order_valid'],
                'order_total' => $r['order_total'],
                'order_total_valid' => $r['order_total_valid'],
                'order_success' => $r['order_success'],
                'real_merchant' => $r['real_merchant'],
                'real_platform' => $r['real_platform'],
            ]);
        }
    }
}