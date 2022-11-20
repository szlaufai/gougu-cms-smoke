<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

namespace app\admin\model;

use think\Model;

class Config extends Model
{
    /**
     * 获取配置
     * @param $name
     * @return array
     */
    public function getByName($name)
    {
        $info = $this::where([['name','=',$name]])->find();
        $config = [];
        if ($info['content']) {
            $config = unserialize($info['content']);
        }
        return $config;
    }
}
