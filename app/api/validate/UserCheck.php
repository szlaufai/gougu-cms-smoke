<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */

namespace app\api\validate;

use think\Validate;

class UserCheck extends Validate
{
    protected $rule = [
        'type' => 'require',
        'email' => 'require',
        'password' => 'require',
//        'captcha' => 'require|captcha',
    ];

    protected $message = [
        'type.require' => '用户类型不能为空',
        'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式不正确',
        'password.require' => '密码不能为空',
        'captcha.require' => '验证码不能为空',
//        'captcha.captcha' => '验证码不正确',
    ];

    protected $scene = [
        'reg' => ['type', 'email', 'password', 'captcha'],
        'login' => ['email', 'password', 'captcha'],
        'resetPassword' => ['email', 'password', 'captcha'],
    ];
}
