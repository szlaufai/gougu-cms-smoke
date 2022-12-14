<?php

declare (strict_types = 1);
namespace app\api\controller;


use app\admin\model\Config;
use app\api\BaseController;
use app\api\middleware\Auth;
use app\api\validate\PointCheck;
use app\model\PointsRecord;
use app\model\User;
use app\model\Voucher;
use think\db\exception\DbException;
use think\exception\ValidateException;
use think\facade\Log;

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

    /**
     * 积分兑换代金券
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function exchangeVoucher(){
        $params = get_params();
        try {
            validate(PointCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }

        $user = User::find(JWT_UID);
        $voucher = Voucher::find($params['voucher_id']);
        if (!$voucher){
            $this->apiError('Coupon is out of stock');
        }
        if ($user['points'] - $user['lock_points'] - $voucher['deduct_points'] < 0){
            $this->apiError('Insufficient point');
        }

        $time = time();
        PointsRecord::startTrans();
        try {
            $pointsData = [
                'user_id' => $user['id'],'type'=>2,'voucher_id'=>$voucher['id'],'status'=>1,
                'quantity' => $voucher['deduct_points'] * (-1),'create_time'=>$time,'remark'=>''
            ];
            PointsRecord::insert($pointsData);

            $voucherData = [
                'status'=>1, 'update_time'=>$time
            ];
            Voucher::where('id', $voucher['id'])->update($voucherData);

            $userData = ['points'=>$user['points'] + $pointsData['quantity'],'update_time'=>$time];
            User::where('id', $user['id'])->update($userData);

            PointsRecord::commit();
            $this->apiSuccess();
        } catch(DbException $e) {
            PointsRecord::rollback();
            Log::error('积分兑换代金券异常'.json_encode(['error'=>$e->getMessage()]));
            $this->apiError('System error, please try later.');
        }
    }

    /**
     * 积分兑换现金
     */
    public function exchangeMoney(){
        $params = get_params();
        try {
            validate(PointCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $user = User::findOrEmpty(JWT_UID);
        $ratio = get_system_config('points2money','ratio');
        if (!$ratio || empty($ratio) || $ratio < 0){
            Log::error('未配置积分现金兑换规则',['config_name'=>'points2money']);
            $this->apiError('System error, please try later.');
        }
        $deductPoints = $params['money'] * $ratio;
        if ($user['points'] - $user['lock_points'] - $deductPoints < 0){
            $this->apiError('Insufficient point');
        }
        $time = time();
        PointsRecord::startTrans();
        try {
            $pointsData = [
                'user_id' => $user['id'],'type'=>3,'money_amount'=>$params['money'],'status'=>0,
                'quantity' => $deductPoints * (-1),'create_time'=>$time,'remark'=>''
            ];
            PointsRecord::insert($pointsData);

            $userData = ['lock_points' => $user['lock_points'] + $deductPoints,'update_time'=>$time];
            User::where('id', $user['id'])->update($userData);

            PointsRecord::commit();
            $this->apiSuccess();
        } catch(DbException $e) {
            PointsRecord::rollback();
            Log::error('积分兑换现金异常'.json_encode(['error'=>$e->getMessage()]));
            $this->apiError('System error, please try later.');
        }
    }

    public function listConvertibleMoney(){
        $moneyList = [5,10,25,50,100,200];
        $ratio = get_system_config('points2money','ratio');
        if (!$ratio || empty($ratio) || $ratio < 0){
            Log::error('未配置积分现金兑换规则',['config_name'=>'points2money']);
            $this->apiSuccess([]);
        }
        $data = [];
        foreach ($moneyList as $money){
            $data[] = [
                'money' => $money,
                'deduct_points' => $money * $ratio,
                'remark' => ''
            ];
        }
        $this->apiSuccess($data);
    }
}