<?php

namespace app\admin\validate;

use think\Validate;

class AdminCheck extends Validate
{
	protected $regex = [ 'checkUser' => '/^[A-Za-z]{1}[A-Za-z0-9_-]{4,19}$/'];
	
    protected $rule = [
        'username' => 'require|regex:checkUser|unique:admin',
        'pwd' => 'require|min:6|confirm',
        'edit_pwd' => 'min:6|confirm',
        'mobile' => 'require',
        'nickname' => 'require',
        'group_id' => 'require',
        'id' => 'require',
        'status' => 'require|checkStatus:-1,1',
        'old_pwd' => 'require|different:pwd',
    ];

    protected $message = [
        'username.require' => 'username is required',
        'username.regex' => 'The login account must start with a letter, and can only contain alphanumeric underscores and minus signs, 5 to 20 characters',
        'username.unique' => 'username already exists',
        'pwd.require' => 'password is required',
        'pwd.min' => 'Please enter a password of at least 8 characters',
        'pwd.confirm' => "The two passwords don't match",
		'edit_pwd.min' => 'Please enter a password of at least 8 characters',
        'edit_pwd.confirm' => "The two passwords don't match",
        'mobile.require' => 'mobile is required',
        'nickname.require' => 'nickname is required',
        'group_id.require' => 'user role is required',
        'id.require' => 'id is required',
        'status.require' => 'status is required',
        'status.checkStatus' => "Super admin can't be disabled",
        'old_pwd.require' => 'old password is required',
        'old_pwd.different' => 'The new password cannot be consistent with the old password',
    ];

    protected $scene = [
        'add' => ['nickname', 'group_id', 'pwd', 'username', 'status'],
        'edit' => ['nickname', 'group_id', 'edit_pwd','id', 'username', 'status'],
        'editPersonal' => ['nickname'],
        'editpwd' => ['old_pwd', 'pwd'],
    ];

    // 自定义验证规则
    protected function checkStatus($value, $rule, $data)
    {
        if ($value == -1 and $data['id'] == 1) {
            return $rule == false;
        }
        return $rule == true;
    }
}
