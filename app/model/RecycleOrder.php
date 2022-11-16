<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

namespace app\model;

use think\Model;

class RecycleOrder extends Model
{
    public static function buildNo($uid){
        $uidStr = $uid < 100000
            ? substr("000000".$uid, -6)
            : $uid;
        $time = time_format(time(),'Ymdhis');
        $random = make_random_number(4);
        return $time.$uidStr.$random;
    }
}
