<?php

namespace app\admin\validate;

use think\Validate;

class ConfCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:config,title^status',
        'name' => 'require|alphaDash|unique:config,name^status',
    ];

    protected $message = [
        'title.require' => 'title is required',
        'title.unique' => 'title already exists',
        'name.require' => 'name is required',
        'name.alphaDash' => 'name can only be letters and numbers, underlined_ And dash-',
        'name.unique' => 'name already exists',
    ];
}
