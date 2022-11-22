<?php

namespace app\admin\model;

use think\Model;

class Config extends Model
{
    /**
     * 获取配置
     * @param $name
     * @return array
     */
    public static function getByName($name)
    {
        $info = self::where([['name','=',$name]])->findOrEmpty();
        $config = [];
        if ($info['content']) {
            $config = unserialize($info['content']);
        }
        return $config;
    }
}
