<?php

namespace app\admin\validate;

use think\Validate;

class UserCheck extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'captcha' => 'require|captcha',
    ];

    protected $message = [
        'username.require' => 'username is required',
        'password.require' => 'password is required',
        'captcha.require' => 'captcha is required',
        'captcha.captcha' => 'captcha not correct',
    ];
}
