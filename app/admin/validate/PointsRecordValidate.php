<?php

namespace app\admin\validate;
use think\Validate;

class PointsRecordValidate extends Validate
{
    protected $rule = [
    'type' => 'require',
];

    protected $message = [
    'type.require' => 'type is required',
];
}