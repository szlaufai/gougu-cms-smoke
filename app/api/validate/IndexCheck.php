<?php

namespace app\api\validate;

use think\Validate;

class IndexCheck extends Validate
{
    protected $rule = [
        'type' => 'require',
        'email' => 'require|email',
        'password' => 'require',
        'captcha' => 'require|captcha',
        'code' => 'require',
        'amount' => 'require|between:0.3,999999.99'
    ];

    protected $message = [
        'type.require' => '用户类型不能为空',
        'email.require' => '邮箱不能为空',
        'password.require' => '密码不能为空',
        'captcha.require' => '验证码不能为空',
        'captcha.captcha' => '验证码不正确',
        'code.require' => '验证码不能为空',
    ];

    protected $scene = [
        'reg' => ['type', 'email', 'password', 'code'],
        'login' => ['email', 'password'],
        'resetPassword' => ['email', 'password'],
        'sendVerifyCode' => ['email'],
        'checkVerifyCode' => ['code','email'],
        'getStripeKey' => ['amount','email'],
    ];
}
