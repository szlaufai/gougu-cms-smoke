<?php

namespace app\admin\model;
use app\model\User;
use app\model\Voucher;
use think\db\exception\DbException;
use think\model;
class PointsRecord extends Model
{
    public static $statusEnum = [
        '-1' => 'Deleted',
        '0' => 'Waiting for approval',
        '1' => 'Approved'
    ];

    public static $typeEnum = [
        '1' => 'Points obtained',
        '2' => 'Redeem voucher',
        '3' => 'Redeem cash',
    ];

    /**
    * 获取分页列表
    * @param $param
    */
    public function getPointsRecordList($param)
    {
        $tableName = $this->getTable();
        $userModel = new User();
        $userTableName = $userModel->getTable();
        $orderModel = new RecycleOrder();
        $orderTableName = $orderModel->getTable();
        $voucherModel = new Voucher();
        $voucherTableName = $voucherModel->getTable();

        $where = [["$tableName.status",'<>','-1']];
        !empty($param['keywords']) && $where[] = ['email|order_no|express_no', 'like', '%' . $param['keywords'] . '%'];
        !empty($param['type']) && $where[] = ["$tableName.type", '=', $param['type']];
        //按时间检索
        $start_time = !empty($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
        $end_time = !empty($param['end_time']) ? strtotime(urldecode($param['end_time'])) + 86400 : 0;

        if ($start_time > 0 && $end_time > 0) {
            if ($start_time === $end_time) {
                $where[] = ["$tableName.create_time", '=', $start_time];
            } else {
                $where[] = ["$tableName.create_time", '>=', $start_time];
                $where[] = ["$tableName.create_time", '<=', $end_time];
            }
        } elseif ($start_time > 0 && $end_time == 0) {
            $where[] = ["$tableName.create_time", '>=', $start_time];
        } elseif ($start_time == 0 && $end_time > 0) {
            $where[] = ["$tableName.create_time", '<=', $end_time];
        }

        $limit = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
        $fields = ["$tableName.id","$tableName.create_time","email","user_no","order_no","express_no","money_amount",
            "$tableName.quantity", "$tableName.remark","$tableName.status","$tableName.type"];
        $list = $this
            ->leftJoin("$userTableName","$tableName.user_id = $userTableName.id")
            ->leftJoin("$orderTableName","$tableName.order_id = $orderTableName.id")
            ->leftJoin("$voucherTableName","$tableName.voucher_id = $voucherTableName.id")
            ->field($fields)->where($where)->order("create_time desc")->paginate($limit);

        $this->fillStatusLabel($list);
        $this->fillTypeLabel($list);
		return $list;
    }

    /**
    * 添加数据
    * @param $param
    */
    public function addPointsRecord($param)
    {
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
    * 编辑信息
    * @param $param
    */
    public function editPointsRecord($param)
    {
        try {
            $param['update_time'] = time();
            $this->where('id', $param['id'])->strict(false)->field(true)->update($param);
			add_log('edit', $param['id'], $param);
        } catch(\Exception $e) {
			return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
		return to_assign();
    }
	

    /**
    * 根据id获取信息
    * @param $id
    */
    public function getPointsRecordById($id)
    {
        $info = $this::with(['user','recycleOrder'])->where('id', $id)->select();
        $this->fillStatusLabel($info);
        $this->fillTypeLabel($info);
		return $info[0];
    }

    /**
    * 删除信息
    * @param $id
    */
    public function delPointsRecordById($id)
    {
        $record = $this->find($id);
        if($record['status'] == '-1'){
            return to_assign(1, 'This data has been deleted');
        }
        if($record['status'] == '1'){
            return to_assign(1, 'Approved data is not allowed to be deleted');
        }
        $this->startTrans();
        try {
            $this->where('id', $id)->update(['status'=>'-1','update_time'=>time()]);
            User::where('id', $record['user_id'])->inc('lock_points', $record['quantity'])->update();
            $this->commit();
            add_log('delete', $id);
        } catch(DbException $e) {
            $this->rollback();
            return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
		return to_assign();
    }

    /**
     * 审核通过
     * @param $id
     */
    public function approved($id)
    {
        $record = $this->find($id);
        if($record['status'] == '-1'){
            return to_assign(1, 'This data has been deleted');
        }
        if($record['status'] == '1'){
            return to_assign(1, 'This data has been approved');
        }
        $this->startTrans();
        try {
            $this->where('id', $id)->update(['status'=>'1','update_time'=>time()]);
            User::where('id', $record['user_id'])->inc('lock_points', $record['quantity'])->inc('points', $record['quantity'])->update();
            $this->commit();
            add_log('approved', $id);
        } catch(DbException $e) {
            $this->rollback();
            return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
        return to_assign();
    }

    public function user(){
        $fields = ['first_name','last_name','email','user_no'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function recycleOrder(){
        $fields = ['order_no','express_no','last_name'];
        return $this->hasOne(RecycleOrder::class,'id','user_id')->bind($fields);
    }

    public function fillStatusLabel(&$rows,$field='status'){
        foreach ($rows as &$row){
            $row['status_label'] = self::$statusEnum[$row[$field]];
        }
    }

    public function fillTypeLabel(&$rows,$field='type'){
        foreach ($rows as &$row){
            $row['type_label'] = self::$typeEnum[$row[$field]];
        }
    }
}

