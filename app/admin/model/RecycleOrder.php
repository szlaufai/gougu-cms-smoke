<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
namespace app\admin\model;
use app\model\PointsRecord;
use think\model;
class RecycleOrder extends Model
{
    public $statusEnum = [
        '-1' => '已删除',
        '0' => '已取消',
        '1' => '运输中',
        '2' => '已完成',
    ];

    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function getRecycleOrderList($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        $list = $this::with('user')->where($where)->field('id,user_id,order_no,express_no,weight,quantity,points,pics,remark,status,create_time')->order($order)->paginate($rows, false, ['query' => $param]);
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
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['aid'=>$insertId]);
    }

    /**
    * 编辑信息
    * @param $param
    */
    public function editRecycleOrder($param)
    {
        try {
            $param['update_time'] = time();
            $fields = ['weight','quantity','pics','remark','status'];
            $this->where('id', $param['id'])->strict(false)->field($fields)->update($param);
			add_log('edit', $param['id'], $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
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
                    'quantity' => $param['points'] - $oldData['points'],'create_time'=>time(),'remark'=>'修改订单积分'
                ];
                PointsRecord::insert($pointsData);
                $userData = ['points'=>$user['points'] + $pointsData['quantity'],'update_time'=>time()];
                User::where('id', $oldData['user_id'])->update($userData);
            }
            $this->commit();
        } catch(\Exception $e) {
            $this->rollback();
            return to_assign(1, '操作失败，原因：'.$e->getMessage());
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
    * 删除信息
    * @param $id
    * @return array
    */
    public function delRecycleOrderById($id,$type=0)
    {
		if($type==0){
			//逻辑删除
			try {
				$param['delete_time'] = time();
				$this->where('id', $id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				$this->where('id', $id)->delete();
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }

    public function user(){
        $fields = ['first_name','email','last_name'];
        return $this->hasOne(User::class,'id','user_id')->bind($fields);
    }

    public function fillStatusLabel(&$rows,$field='status'){
        foreach ($rows as &$row){
            $row['status_label'] = $this->statusEnum[$row[$field]];
        }
    }
}

