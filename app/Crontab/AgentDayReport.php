<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;


#[Crontab(rule: "0 *\/5 * * * *", name: "AgentDayReport", callback: "execute", memo: "代理报表", enable: true)]
class AgentDayReport
{

    public function AgentDayReport()
    {
        $this->execute();
    }

    public function execute()
    {
        $startTime = time() - 600;
        $today = date("Y-m-d", $startTime);
        $list = Db::table("merchants")->where("name", "!=", "admin")->get();
        //不同的通道不同的点位。 每个订单要给的点位
        $parentList = [];
        foreach ($list as $v) {
            //代理有2种盈利方式？1是自身的点差？ 2是下级给的佣金
            $pay = Db::select("select sum(amount_paid) as pay_money,count(*) as pay_num ,sum(parent_rate*amount_paid) as parent_money from orders where finished>=? and state=2 and merchant_id=? ", [$startTime, $v->id]);
            $withdraw = Db::select("select sum(amount_paid) as withdraw_money,count(*) as withdraw_num,sum(parent_rate*amount_paid) as parent_money  from payouts where finished>=? and state=1 and merchant_id=? ", [$startTime, $v->id]);
            $dayReport = Db::table("agent_day_report")->where('day', "=", $today)->where("merchant_id", $v->id)->first();
            if (!$dayReport) {
                Db::insert("insert into agent_day_report(day,merchant_id,merchant_name) values(?,?,?)", [$today, $v->id, $v->name]);
            }

            Db::update("update agent_day_report set pay_money=pay_money+?,pay_num=pay_num+?,withdraw_money=withdraw_money+?,withdraw_num=withdraw_num+? where day=? and merchant_id=? ", [
                $pay[0]->pay_money ?? 0,
                $pay[0]->pay_num,
                $withdraw[0]->withdraw_money ?? 0,
                $withdraw[0]->withdraw_num,
                $today,
                $v->id,
            ]);
            //还有个差价的登确认再添加
            //还有2个手续费是啥玩意
            $parentList[$v->parent_id] = $pay[0]->parent_money ?? 0 + $withdraw[0]->parent_money ?? 0;
            //  array_push($parentList, [$v->parent_id => $pay[0]->parent_money + $withdraw[0]->parent_money]);
        }

        foreach ($parentList as $k => $val) {
            Db::update("update agent_day_report set agent_money=agent_money+? where day=? and merchant_id", [$val, $k]);
        }
    }
}
