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
        'name.require' => 'name is required',
        'mobile.require' => 'mobile is required',
        'city.require' => 'city is required',
        'address.require' => 'address is required',
        'postcode.require' => 'postcode is required',
        'goods_name.require' => 'goods_name is required',
        'order_id.require' => 'order_id is required',
        'express_no.require' => 'express_no is required',
    ];

    protected $scene = [
        'create' => ['name', 'address', 'mobile', 'goods_name','postcode','city'],
        'get' => ['order_id'],
        'cancel' => ['order_id'],
        'getTracking' => ['express_no'],
        'getLabelFile' => ['express_no'],
    ];
}
