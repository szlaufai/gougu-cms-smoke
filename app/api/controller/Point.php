<?php

declare (strict_types = 1);
namespace app\api\controller;


use app\api\BaseController;
use app\api\middleware\Auth;
use app\model\PointsRecord;
use app\model\User;

class Point extends BaseController
{
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        Auth::class
    ];

    public function page(){

        $user = User::findOrEmpty(JWT_UID);
        $params = get_params();
        $where = [['user_id','=',$user['id']],['status','>=',0]];
        $fields = [
            'id','order_id','type','voucher_id','money_amount','quantity','remark','status','create_time','update_time'
        ];
        $list = PointsRecord::with('recycle_order')->where($where)->order('id', 'desc')->field($fields)
            ->paginate($params['size'] ?? 10);
        $this->apiSuccess($list);
    }
}