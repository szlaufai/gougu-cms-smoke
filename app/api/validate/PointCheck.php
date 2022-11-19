<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

namespace app\api\validate;

use think\Validate;

class PointCheck extends Validate
{
    protected $rule = [
        'voucher_id' => 'require',
    ];

    protected $message = [
        'voucher_id.require' => '代金券ID不能为空',
    ];

    protected $scene = [
        'exchangeVoucher' => ['voucher_id'],
        'exchangeMoney' => ['money'],
    ];
}
