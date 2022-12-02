<?php

namespace app\admin\validate;
use think\Validate;

class VoucherValidate extends Validate
{
    protected $rule = [
    'code' => 'require',
    'value' => 'require',
    'deduct_points' => 'require',
];

    protected $message = [
    'code.require' => 'code is required',
    'value.require' => 'value is required',
    'deduct_points.require' => 'deduct points is required',
];
}