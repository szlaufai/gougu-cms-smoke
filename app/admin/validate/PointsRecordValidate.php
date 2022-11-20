<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

namespace app\admin\validate;
use think\Validate;

class PointsRecordValidate extends Validate
{
    protected $rule = [
    'type' => 'require',
];

    protected $message = [
    'type.require' => '类型不能为空',
];
}