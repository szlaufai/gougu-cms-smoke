<?php

namespace app\admin\model;
use app\model\PointsRecord;
use think\db\exception\DbException;
use think\facade\Log;
use think\model;
class RecycleOrder extends Model
{
    public static $statusEnum = [
        '-1' => 'Deleted',
        '0' => 'Cancelled',
        '1' => 'In delivery',
        '2' => 'Completed',
    ];

    /**
    * 获取分页列表
    * @param $param
    */
    public function getRecycleOrderList($param)
    {
        $tableName = $this->getTable();
        $userModel = new User();
        $userTableName = $userModel->getTable();

        $where = [["$tableName.status",'<>','-1']];
        !empty($param['keywords']) && $where[] = ['email|order_no|express_no', 'like', '%' . $param['keywords'] . '%'];
        //按时间检索
        $start_time = !empty($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
        $end_time = !empty($param['end_time']) ? strtotime(urldecode($param['end_time'])) + 86400 : 0;

        if ($start_time > 0 && $end_time > 0) {
            if ($start_time === $end_time) {
                $where[] = ['create_time', '=', $start_time];
            } else {
                $where[] = ['create_time', '>=', $start_time];
                $where[] = ['create_time', '<=', $end_time];
            }
        } elseif ($start_time > 0 && $end_time == 0) {
            $where[] = ['create_time', '>=', $start_time];
        } elseif ($start_time == 0 && $end_time > 0) {
            $where[] = ['create_time', '<=', $end_time];
        }

		$limit = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
        $fields = ["$tableName.id","create_time","email","user_no","order_no","express_no","weight","quantity",
            "$tableName.points","pics","remark","$tableName.status"];
        $list = $this->leftJoin("$userTableName","$tableName.user_id = $userTableName.id")
            ->field($fields)->where($where)->order("create_time desc")->paginate($limit);
        $this->fillStatusLabel($list);
		return $list;
    }

    /**
    * 添加数据
    * @param $param
    */
    public function addRecycleOrder($param)
    {
		$insertId = 0;
        try {
			$param['create_time'] = time();
			$insertId = $this->strict(false)->field(true)->insertGetId($param);
			add_log('add', $insertId, $param);
        } catch(\Exception $e) {
			return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
		return to_assign(0,'Operation succeeds',['aid'=>$insertId]);
    }

    /**
    * 收货确认
    * @param $param
    */
    public function receiptConfirm($param)
    {
        $order = $this->find($param['id']);
        $user = User::find($order['user_id']);
        if($order['status'] == '-1'){
            return to_assign(1, 'This order has been deleted');
        }
        if($order['status'] == '0'){
            return to_assign(1, 'This order has been canceled');
        }
        if($order['status'] == '2'){
            return to_assign(1, 'This order has been approved');
        }
        $ratio = get_system_config('weight2points','ratio');
        if (!$ratio || empty($ratio) || $ratio < 0){
            Log::error('未配置重量积分转换规则',['config_name'=>'weight2points']);
            return to_assign(1,'please set the weight2points config');
        }
        $data = [
            'weight' => $param['weight'],
            'quantity' => $param['quantity'],
            'points' => round($param['weight'] * $ratio,2),
            'pics' => $param['pics'] ?? "",
            'remark' => $param['remark'] ?? "",
            'status' => 2,
            'update_time' => time()
        ];
        $this->startTrans();
        try {
            $order::where('id', $order['id'])->update($data);
            $pointsData = [
                'user_id' => $order['user_id'],'type'=>1,'order_id'=>$order['id'],
                'quantity' => $data['points'],'create_time'=>time()
            ];
            PointsRecord::insert($pointsData);
            $userData = ['points'=>$user['points'] + $data['points'],'update_time'=>time()];
            User::where('id', $order['user_id'])->update($userData);
            $this->commit();
        } catch(DbException $e) {
            $this->rollback();
            Log::error('收货确认异常'.json_encode(['error'=>$e->getMessage(),'params'=>$param]));
			return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
        add_log('receipt', $param['id'], $param);
		return to_assign();
    }

    /**
     * 超级管理员编辑信息
     * @param $param
     */
    public function editRecycleOrderByRoot($param)
    {
        $oldData = $this->findOrEmpty($param['id']);
        $user = User::find($oldData['user_id']);
        $this->startTrans();
        try {
            $param['update_time'] = time();
            $fields = ['weight','quantity','points','pics','remark','status'];
            $this->where('id', $param['id'])->strict(false)->field($fields)->update($param);
            if ($oldData['points'] != $param['points']){
                $pointsData = [
                    'user_id' => $oldData['user_id'],'type'=>1,'order_id'=>$oldData['id'],
                    'quantity' => $param['points'] - $oldData['points'],'create_time'=>time()
                ];
                $pointsData['remark'] = $oldData['status'] == 2 && $param['status'] == 2 ? "edit order points" : "";
                PointsRecord::insert($pointsData);
                $userData = ['points'=>$user['points'] + $pointsData['quantity'],'update_time'=>time()];
                User::where('id', $oldData['user_id'])->update($userData);
            }
            $this->commit();
        } catch(DbException $e) {
            $this->rollback();
            Log::error('编辑订单异常'.json_encode(['error'=>$e->getMessage(),'params'=>$param]));
            return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
        add_log('edit', $param['id'], $param);
        return to_assign();
    }

    /**
    * 根据id获取信息
    * @param $id
    */
    public function getRecycleOrderById($id)
    {
        $info = $this::with('user')->where('id', $id)->find();
		return $info;
    }

    /**
     * 根据快递追踪号获取详情
     * @param $id
     */
    public function getRecycleOrderByExpress($expressNo)
    {
        $info = $this::where('express_no', $expressNo)->findOrEmpty();
        return $info;
    }

    /**
    * 删除信息
    * @param $id
    */
    public function delRecycleOrderById($id,$type=0)
    {
        $record = $this->find($id);
        if($record['status'] == '-1'){
            return to_assign(1, 'This data has been deleted');
        }
        if($record['status'] != '0'){
            return to_assign(1, "Deletion is not allowed due to data's status");
        }
        try {
            $this->where('id', $id)->update(['status'=>'-1','update_time'=>time()]);
            add_log('delete', $id);
        } catch(\Exception $e) {
            return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
        return to_assign();
    }

    public function user(){
        $fields = ['first_name','last_name','email','user_no'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function fillStatusLabel(&$rows,$field='status'){
        foreach ($rows as &$row){
            $row['status_label'] = self::$statusEnum[$row[$field]];
        }
    }
}

