<?php

namespace app\admin\model;

use think\db\exception\DbException;
use think\Model;

class User extends Model
{
    public $typeEnum = [
        '1' => '普通用户',
        '2' => '商户用户',
    ];

    public $statusEnum = [
        '-1' => '已删除',
        '0' => '已禁用',
        '1' => '正常',
    ];

    public $approvalStatusEnum = [
        '0' => '待审核',
        '1' => '已审核',
    ];

    public function fillStatusLabel(&$rows,$field='status'){
        foreach ($rows as &$row){
            $row['status_label'] = $this->statusEnum[$row[$field]];
        }
    }

    public function fillApprovalStatusLabel(&$rows,$field='approval_status'){
        foreach ($rows as &$row){
            $row['approval_status_label'] = $this->approvalStatusEnum[$row[$field]];
        }
    }

    public function fillTypeLabel(&$rows,$field='type'){
        foreach ($rows as &$row){
            $row['type_label'] = $this->typeEnum[$row[$field]];
        }
    }

    public function dataList($where,$param){
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $content = $this->where($where)
            ->order('id desc')
            ->paginate($rows, false)
            ->each(function ($item, $key) {
                $item->register_time = empty($item->register_time) ? '-' : date('Y-m-d H:i', $item->register_time);
            });
        $this->fillStatusLabel($content);
        $this->fillApprovalStatusLabel($content);
        $this->fillTypeLabel($content);
        return table_assign(0, '', $content);
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
        if($record['approval_status'] == '1'){
            return to_assign(1, '此数据已审核通过');
        }
        try {
            $this->where('id', $id)->update(['approval_status'=>'1','update_time'=>time()]);
            add_log('approved', $id);
        } catch(DbException $e) {
            return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
        return to_assign();
    }
}
