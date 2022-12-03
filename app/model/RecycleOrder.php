<?php

namespace app\model;

use think\Model;

class RecycleOrder extends Model
{
    public static function buildNo(){
        $date = date('ymd');
        $last = self::where('order_no','like',$date.'%')->field(['order_no'])->order('order_no desc')->find();
        $number = 1;
        if ($last){
            $number = (int)str_replace($date,'',$last['order_no']) + 1;
        }
        $numberStr = $number < 1000
            ? substr("0000".$number, -4)
            : $number;
        return $date.$numberStr;
    }

    public function user(){
        $fields = ['user_no','email','first_name','last_name','points', 'lock_points'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function userBase(){
        $fields = ['type','first_name','last_name','address','longitude','latitude'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }
}
