<?php

declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\api\middleware\Auth;
use app\model\User as UserModel;
use think\App;


class User extends BaseController
{
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        Auth::class
    ];

    public function get(){
        $fields = [
            'id','user_no','email','first_name','last_name','mobile','headimgurl','sex','desc','country','province','city',
            'company','company_tax_code', 'address','detail_address','longitude','latitude','paypal_name','paypal_account',
            'points','lock_points','postcode'
        ];
        $user = UserModel::field($fields)->find(JWT_UID);
        $this->apiSuccess($user);
    }

    public function update(){
        $params = get_params();
        $user = UserModel::find(JWT_UID);
        $fields = [
            'first_name','last_name','mobile','headimgurl','sex','desc','country','province','city','company','company_tax_code',
            'address','detail_address','longitude','latitude','paypal_name','paypal_account','postcode'
        ];
        $user->allowField($fields)->save($params);
        $this->apiSuccess();
    }
}
