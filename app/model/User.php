<?php

namespace app\model;

use think\Model;

class User extends Model
{
    public static function buildNo($id){
        $code = $id < 1000
            ? substr("0000".$id, -4)
            : $id;
        return "GW$code";
    }
}
