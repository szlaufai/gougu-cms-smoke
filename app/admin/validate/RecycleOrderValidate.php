<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

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
    'id.require' => '数据ID不能为空',
    'order_no.require' => '订单编号不能为空',
    'express_no.require' => '快递单号不能为空',
    'weight.require' => '重量不能为空',
    'points.require' => '积分不能为空',
];
}