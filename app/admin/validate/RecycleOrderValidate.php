<?php

namespace app\admin\validate;
use think\Validate;

class RecycleOrderValidate extends Validate
{
    protected $rule = [
    'id' => 'require',
    'order_no' => 'require',
    'express_no' => 'require',
    'weight' => 'require',
    'points' => 'require',
];

    protected $message = [
    'id.require' => 'id is required',
    'order_no.require' => 'order no is required',
    'express_no.require' => 'express no is required',
    'weight.require' => 'weight is required',
    'points.require' => 'points is required',
];
}