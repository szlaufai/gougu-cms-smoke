<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

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