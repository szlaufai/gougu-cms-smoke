<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
namespace app\model;

use think\Model;

class Voucher extends Model
{
    public $statusEnum = [
        '-1' => '已删除',
        '0' => '待兑换',
        '1' => '已兑换',
    ];

    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function getVoucherList($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        $list = $this->where($where)->field('id,code,passwd,value,deduct_points,pics,remark,status')->order($order)->paginate($rows, false, ['query' => $param]);
        $this->fillStatusLabel($list);
		return $list;
    }

    /**
    * 添加数据
    * @param $param
    */
    public function addVoucher($param)
    {
		$code = trim($param['code']);
		$voucher = $this->where([['code','=',$code],['status','<>','-1']])->find();
		if ($voucher){
            return to_assign(1, '券码已存在');
        }
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
    public function editVoucher($param)
    {
        $voucher = $this->findOrEmpty($param['id']);
        if ($voucher['status'] != '0'){
            return to_assign(1, '此券已兑换，不可修改');
        }
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
    public function getVoucherById($id)
    {
        $info = $this->where('id', $id)->find();
		return $info;
    }

    /**
    * 删除信息
    * @param $id
    */
    public function delVoucherById($id)
    {
        //逻辑删除
        try {
            $this->where('id', $id)->update(['status'=>'-1','update_time'=>time()]);
            add_log('delete', $id);
        } catch(\Exception $e) {
            return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign();
    }

    public function fillStatusLabel(&$rows,$field='status'){
        foreach ($rows as &$row){
            $row['status_label'] = $this->statusEnum[$row[$field]];
        }
    }
}

