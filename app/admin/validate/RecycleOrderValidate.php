<?php

namespace app\admin\validate;
use think\Validate;

class RecycleOrderValidate extends Validate
{
    protected $rule = [
        'id' => 'require',
        'order_no' => 'require',
        'express_no' => 'require',
        'weight' => 'require|between:0.01,9999.99',
        'points' => 'require|between:0.01,9999.99',
        'quantity' => 'between:0,9999',
    ];

    protected $message = [
        'id.require' => 'id is required',
        'order_no.require' => 'order no is required',
        'express_no.require' => 'express no is required',
        'weight.require' => 'weight is required',
        'weight.between' => 'weight must be between 0.01 and 9999.99',
        'quantity.between' => 'quantity must be between 0 and 9999',
        'points.require' => 'points is required',
        'points.between' => 'weight must be between 0.01 and 9999.99',
    ];

    protected $scene = [
        'receipt' => ['weight','quantity', 'id'],
    ];
}