<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;

#[Crontab(rule: "0 *\/10 * * * *", name: "DayReport", callback: "execute", memo: "每日报表", enable: true)]
class DayReport
{

    public function dayReport()
    {
        $this->execute();
    }

    public function execute()
    {
        $startTime = time() - 600;
        $today = date("Y-m-d", $startTime);
        //不同的通道不同的点位。 每个订单要给的点位
        $pay = Db::select("select sum(amount_paid) as pay_money,count(*) as pay_num ,sum(amount_paid*platform_rate) as platform_money,sum(parent_rate*amount_paid) as parent_money from orders where finished>=? and state=2", [$startTime]);
        $withdraw = Db::select("select sum(amount_paid) as withdraw_money,count(*) as withdraw_num, sum(amount_paid*platform_rate) as platform_money,sum(parent_rate*amount_paid) as parent_money  from payouts where finished >=? and state=1", [$startTime]);
        $dayReport = Db::table("report_days")->where('day', "=", $today)->first();
        if (!$dayReport) {
            Db::insert("insert into report_days(day) values(?)", [$today]);
        }

        Db::update("update report_days set pay_money=pay_money+?,pay_num=pay_num+?,withdraw_money=withdraw_money+?,withdraw_num=withdraw_num+?,agent_money=agent_money+?,platform_money=platform_money+? where day=?", [
            $pay[0]->pay_money,
            $pay[0]->pay_num,
            $withdraw[0]->withdraw_money,
            $withdraw[0]->withdraw_num,
            $pay[0]->parent_money + $withdraw[0]->parent_money, //暂时只是一级
            $pay[0]->platform_money + $withdraw[0]->platform_money,
            $today
        ]);
    }
}
