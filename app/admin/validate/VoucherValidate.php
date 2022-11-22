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
    'code.require' => '券码不能为空',
    'value.require' => '面额不能为空',
    'deduct_points.require' => '所需积分不能为空',
];
}