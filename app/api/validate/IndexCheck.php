<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

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
        'reg' => ['type', 'email', 'password'],
        'login' => ['email', 'password'],
        'resetPassword' => ['email', 'password'],
        'sendVerifyCode' => ['email'],
        'checkVerifyCode' => ['code'],
        'getStripeKey' => ['amount'],
    ];
}
