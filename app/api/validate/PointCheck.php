<?php

namespace app\api\validate;

use think\Validate;

class PointCheck extends Validate
{
    protected $rule = [
        'value' => 'require',
        'money' => 'require',
    ];

    protected $message = [
        'value.require' => 'voucher value is required',
        'money.require' => 'money is required',
    ];

    protected $scene = [
        'exchangeVoucher' => ['value'],
        'exchangeMoney' => ['money'],
    ];
}
