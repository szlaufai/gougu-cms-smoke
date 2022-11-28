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
        'amount' => 'require|between:0.3,999999.99',
        'tag' => 'require'
    ];

    protected $message = [
        'type.require' => 'type is required',
        'email.require' => 'email is required',
        'password.require' => 'password is required',
        'captcha.require' => 'captcha is required',
        'captcha.captcha' => 'captcha is incorrect',
        'code.require' => 'code is required',
    ];

    protected $scene = [
        'reg' => ['type', 'email', 'password', 'code'],
        'login' => ['email', 'password'],
        'resetPassword' => ['email', 'password'],
        'sendVerifyCode' => ['email','tag'],
        'checkVerifyCode' => ['code','email'],
        'getStripeKey' => ['amount','email'],
    ];
}
