<?php

declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\api\middleware\Auth;
use app\api\validate\OrderCheck;
use app\facade\XZHMailApi;
use app\model\RecycleOrder;
use think\App;
use app\model\User;
use think\exception\ValidateException;
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
            'id','order_no','express_no','label_url','weight','quantity','points','pics','remark','status','create_time','update_time'
        ];
        $list = RecycleOrder::where($where)->order('id', 'desc')->field($fields)->paginate($params['size'] ?? 10);
        $this->apiSuccess($list);
    }

    public function create(){
        $user = User::findOrEmpty(JWT_UID);
        $params = get_params();
        try {
            validate(OrderCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $params['country'] = $params['country'] ?? env('app_env') == 'prod' ? 'UK' : 'DE';
        try {
            $mailData = XZHMailApi::create($params);
        }catch (\Exception $e){
            Log::error('调用新智慧创建运单服务异常'.json_encode(['error'=>$e->getMessage(),'params'=>$params]));
            $this->apiError('System error, please try later.');
        }
        $time = time();
        $insertData = [
            'user_id' => $user['id'],
            'order_no' => RecycleOrder::buildNo(),
            'shipment_id' => $mailData['shipment']['shipment_id'],
            'create_time' => $time,
            'update_time' => $time,
        ];
        RecycleOrder::strict(false)->field(true)->insert($insertData);
        $this->apiSuccess();
    }

    public function get(){
        $params = get_params();
        try {
            validate(OrderCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $fields = [
            'id','order_no','express_no','weight','quantity','points','pics','remark','status','create_time','update_time'
        ];
        $data = RecycleOrder::field($fields)->where('id',$params['order_id'])->findOrEmpty();
        $this->apiSuccess($data);
    }

    public function getTracking(){
        $params = get_params();
        try {
            validate(OrderCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        try {
            $mailData = XZHMailApi::getTrackingRoute($params['express_no']);
        }catch (\Exception $e){
            Log::error('调用新智慧查询运单路由服务异常'.json_encode(['error'=>$e->getMessage(),'params'=>$params]));
            $this->apiError('System error, please try later.');
        }
        $this->apiSuccess($mailData['shipment']['traces']);
    }

    public function cancel(){
        $params = get_params();
        try {
            validate(OrderCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $order = RecycleOrder::findOrEmpty($params['order_id']);
        if ($order['status'] != 1){
            $this->apiError("The order can't be cancelled with current status");
        }
        try {
            $mailData = XZHMailApi::cancel($order['express_no']);
        }catch (\Exception $e){
            Log::error('调用新智慧取消运单服务异常'.json_encode(['error'=>$e->getMessage(),'params'=>$params]));
            $this->apiError('System error, please try later.');
        }
        $order->save(['status'=>0]);
        $this->apiSuccess();
    }

    public function getLabelFile(){
        $params = get_params();
        try {
            validate(OrderCheck::class)->scene(request()->action())->check($params);
        } catch (ValidateException $e) {
            $this->apiError($e->getMessage());
        }
        $order = RecycleOrder::find($params['order_id']);
        if (!$order){
            throw new \think\exception\HttpException(404, 'File Not Found');
        }
        $file = public_path().$order['label_url'];
        if (file_exists($file)){
            return download($file,$order['express_no'])->force(false);
        }else{
            throw new \think\exception\HttpException(404, 'File Not Found');
        }
    }
}
