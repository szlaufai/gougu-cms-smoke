<?php
namespace app\facade;

use think\Facade;

class PayoutClient extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\PayoutClient';
    }
}