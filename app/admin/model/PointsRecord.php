<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
namespace app\admin\model;
use think\db\exception\DbException;
use think\model;
class PointsRecord extends Model
{
    public $statusEnum = [
        '-1' => '已删除',
        '0' => '待审核',
        '1' => '已审核'
    ];

    public $typeEnum = [
        '1' => '回收积分',
        '2' => '兑换代金券',
        '3' => '兑换现金',
    ];

    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function getPointsRecordList($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        $list = $this::with(['user','recycleOrder'])->where($where)->field('id,user_id,order_id,type,voucher_id,money_amount,quantity,remark,status')->order($order)->paginate($rows, false, ['query' => $param]);
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
		$insertId = 0;
        try {
			$param['create_time'] = time();
			$insertId = $this->strict(false)->field(true)->insertGetId($param);
			add_log('add', $insertId, $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['aid'=>$insertId]);
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
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign();
    }
	

    /**
    * 根据id获取信息
    * @param $id
    */
    public function getPointsRecordById($id)
    {
        $info = $this::with(['user','recycleOrder'])->where('id', $id)->find();
		return $info;
    }

    /**
    * 删除信息
    * @param $id
    * @return array
    */
    public function delPointsRecordById($id)
    {
        $record = $this->find($id);
        if($record['status'] == '-1'){
            return to_assign(1, '此数据已删除');
        }
        if($record['status'] == '1'){
            return to_assign(1, '已审核的数据不允许删除');
        }
        try {
            $this->where('id', $id)->update(['status'=>'-1','update_time'=>time()]);
            add_log('delete', $id);
        } catch(\Exception $e) {
            return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign();
    }

    /**
     * 审核通过
     * @param $id
     * @return array
     */
    public function approved($id)
    {
        $record = $this->find($id);
        if($record['status'] == '-1'){
            return to_assign(1, '此数据已删除');
        }
        if($record['status'] == '1'){
            return to_assign(1, '此数据已审核通过');
        }
        try {
            $this->where('id', $id)->update(['status'=>'1','update_time'=>time()]);
            add_log('approved', $id);
        } catch(DbException $e) {
            return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
        return to_assign(0, "操作成功",['status'=>1,'status_label'=>$this->statusEnum['1']]);
    }

    public function user(){
        $fields = ['first_name','email','last_name'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function recycleOrder(){
        $fields = ['order_no','express_no','last_name'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function fillStatusLabel(&$rows,$field='status'){
        foreach ($rows as &$row){
            $row['status_label'] = $this->statusEnum[$row[$field]];
        }
    }

    public function fillTypeLabel(&$rows,$field='type'){
        foreach ($rows as &$row){
            $row['type_label'] = $this->typeEnum[$row[$field]];
        }
    }
}

