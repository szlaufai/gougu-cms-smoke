<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

namespace app\model;

use think\Model;

class PointsRecord extends Model
{
    public function user(){
        $fields = ['first_name','last_name','points', 'lock_points'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function recycleOrder(){
        return $this->hasOne(RecycleOrder::class,'id','order_id')->bind(['order_no', 'express_no']);
    }
}
