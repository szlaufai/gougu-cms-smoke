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
    'type.require' => 'type is required',
    'third_payment_id.require' => 'third payment id is required',
    'amount.require' => 'amount is required',
    'email.require' => 'email is required',
];
}