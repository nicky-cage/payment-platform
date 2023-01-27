<?php

declare(strict_types=1);

namespace App\Common;

use Hyperf\DbConnection\Db;

class Func
{
    //获取各级ID和点位
    public static function getParentPath($user_id, $channelId, $type, &$str)
    {
        // 1. 2,3,4  点位
        $temp = Db::table("merchant_rate")->where("merchant_id", $user_id)->where("channel_id", $channelId)->where("type", $type)->first();
        if ($temp->merchant_name = 'admin') {
            return $temp->merchant_id . ":" . ((float)$temp->lower_rate - (float)$temp->base_rate) . "," . $str;
        }
        //第一级测试一下。
        $str = $temp->merchant_id . ":" . ((float)$temp->lower_rate - (float)$temp->base_rate) . "," . $str;

        self::getParentPath($temp->patent_id, $channelId, $type, $str);
    }
}
