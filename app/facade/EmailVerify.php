<?php
namespace app\facade;

use think\Facade;

class EmailVerify extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\EmailVerify';
    }
}