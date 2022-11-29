<?php

declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\api\middleware\Auth;
use app\model\PointsRecord;
use app\model\User;
use app\model\Voucher as VoucherModel;

class Voucher extends BaseController
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
        $where = [['user_id','=',$user['id']],['type','=',2],['status','=',1]];
        $fields = ['voucher_id'];
        $list = PointsRecord::with('voucher')->where($where)->order('create_time', 'desc')->field($fields)
            ->paginate($params['size'] ?? 10);
        $this->apiSuccess($list);
    }

    public function listConvertibleVoucherType(){
	    $where = [['status','=',0]];
	    $fields = ['value','deduct_points','pics','remark'];
	    $list = VoucherModel::where($where)->field($fields)->group('value')->select();
	    $this->apiSuccess($list);
    }
}
