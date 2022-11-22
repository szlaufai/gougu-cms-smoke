<?php

namespace app\api\validate;

use think\Validate;

class OrderCheck extends Validate
{
    protected $rule = [
        'name' => 'require',
        'address' => 'require',
        'mobile' => 'require',
        'goods_name' => 'require',
        'postcode' => 'require',
        'city' => 'require',
        'order_id' => 'require',
        'express_no' => 'require',
    ];

    protected $message = [
        'name.require' => '发件人姓名不能为空',
        'mobile.require' => '发件人电话不能为空',
        'city.require' => '发件城市不能为空',
        'address.require' => '发件地址不能为空',
        'postcode.require' => '发件邮编不能为空',
        'goods_name.require' => '货品名称不能为空',
        'order_id.require' => '订单ID不能为空',
        'express_no.require' => '运单编号不能为空',
    ];

    protected $scene = [
        'create' => ['name', 'address', 'mobile', 'goods_name','postcode','city'],
        'get' => ['order_id'],
        'cancel' => ['order_id'],
        'getTracking' => ['express_no'],
    ];
}
