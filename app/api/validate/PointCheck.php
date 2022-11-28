<?php

namespace app\api\validate;

use think\Validate;

class PointCheck extends Validate
{
    protected $rule = [
        'voucher_id' => 'require',
    ];

    protected $message = [
        'voucher_id.require' => 'voucher_id is required',
        'money.require' => 'money is required',
    ];

    protected $scene = [
        'exchangeVoucher' => ['voucher_id'],
        'exchangeMoney' => ['money'],
    ];
}
