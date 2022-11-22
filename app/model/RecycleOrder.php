<?php

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

    public function user(){
        $fields = ['first_name','last_name','email','points', 'lock_points'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function userBase(){
        $fields = ['type','first_name','last_name','address','longitude','latitude'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }
}
