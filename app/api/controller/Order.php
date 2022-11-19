<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\api\middleware\Auth;
use app\facade\XZHMailApi;
use app\model\RecycleOrder;
use think\App;
use app\model\User;
use think\facade\Log;


class Order extends BaseController
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
        !empty($params['order_no']) && $where[] = ['order_no','like',$params['order_no']];
        !empty($params['express_no']) && $where[] = ['express_no','like',$params['express_no']];
        $fields = [
            'id','order_no','express_no','weight','quantity','points','pics','remark','status','create_time','update_time'
        ];
        $list = RecycleOrder::where($where)->order('id', 'desc')->field($fields)->paginate($params['size'] ?? 10);
        $this->apiSuccess($list);
    }

    public function create(){
        $user = User::findOrEmpty(JWT_UID);
        $params = get_params();
        try {
            $mailData = XZHMailApi::create($params);
        }catch (\Exception $e){
            Log::error('调用新智慧创建运单服务异常'.json_encode(['error'=>$e->getMessage(),'params'=>$params]));
            $this->apiError('服务异常，请稍后再试');
        }
        $time = time();
        $insertData = [
            'user_id' => $user['id'],
            'order_no' => RecycleOrder::buildNo($user['id']),
            'express_no' => $mailData['shipment']['shipment_id'],
            'create_time' => $time,
            'update_time' => $time,
        ];
        RecycleOrder::strict(false)->field(true)->insert($insertData);
        $this->apiSuccess();
    }

    public function get(){
        $params = get_params();
        $fields = [
            'id','order_no','express_no','weight','quantity','points','pics','remark','status','create_time','update_time'
        ];
        $data = RecycleOrder::field($fields)->where('id',$params['order_id'])->findOrEmpty();
        $this->apiSuccess($data);
    }

    public function getTracking(){
        $params = get_params();
        try {
            $mailData = XZHMailApi::getTracking($params['express_no']);
        }catch (\Exception $e){
            Log::error('调用新智慧查询运单路由服务异常'.json_encode(['error'=>$e->getMessage(),'params'=>$params]));
            $this->apiError('服务异常，请稍后再试');
        }
        $this->apiSuccess($mailData['shipment']);
    }

    public function cancel(){
        $params = get_params();
        $order = RecycleOrder::findOrEmpty($params['order_id']);
        if ($order['status'] != 1){
            $this->apiError('此状态下不可取消');
        }
        try {
            $mailData = XZHMailApi::cancel($order['express_no']);
        }catch (\Exception $e){
            Log::error('调用新智慧取消运单服务异常'.json_encode(['error'=>$e->getMessage(),'params'=>$params]));
            $this->apiError('服务异常，请稍后再试');
        }
        $order->save(['status'=>0]);
        $this->apiSuccess();
    }
}
