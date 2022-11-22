<?php

namespace app\model;

use think\Model;

class PointsRecord extends Model
{
    public function user()
    {
        $fields = ['first_name', 'last_name', 'points', 'lock_points'];
        return $this->hasOne(User::class, 'id', 'user_id')->bind($fields);
    }

    public function recycleOrder()
    {
        return $this->hasOne(RecycleOrder::class, 'id', 'order_id')->bind(['order_no', 'express_no']);
    }

    public function voucher()
    {
        return $this->hasOne(Voucher::class, 'id', 'voucher_id')
            ->bind(['code', 'passwd', 'value','deduct_points','pics','remark']);
    }
}