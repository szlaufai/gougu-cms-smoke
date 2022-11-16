<?php
namespace app\facade;

use think\Facade;

class XZHMailApi extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\XZHMailApi';
    }
}