<?php

namespace app\admin\validate;
use think\Validate;

class DonateRecordValidate extends Validate
{
    protected $rule = [
    'type' => 'require',
    'third_payment_id' => 'require',
    'amount' => 'require',
    'email' => 'require',
];

    protected $message = [
    'type.require' => '类型不能为空',
    'third_payment_id.require' => '第三方支付ID不能为空',
    'amount.require' => '金额不能为空',
    'email.require' => '邮箱不能为空',
];
}