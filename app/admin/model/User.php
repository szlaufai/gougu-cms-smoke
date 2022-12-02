<?php

namespace app\admin\model;

use think\db\exception\DbException;
use think\Model;

class User extends Model
{
    public $typeEnum = [
        '1' => 'Individual user',
        '2' => 'Merchants',
    ];

    public $statusEnum = [
        '-1' => 'Deleted',
        '0' => 'Forbidden',
        '1' => 'Normal',
    ];

    public $approvalStatusEnum = [
        '0' => 'Waiting for approval',
        '1' => 'Approved',
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
     */
    public function approved($id)
    {
        $record = $this->find($id);
        if($record['status'] == '-1'){
            return to_assign(1, 'This data has been deleted');
        }
        if($record['approval_status'] == '1'){
            return to_assign(1, 'This data has been approved');
        }
        try {
            $this->where('id', $id)->update(['approval_status'=>'1','update_time'=>time()]);
            add_log('approved', $id);
        } catch(DbException $e) {
            return to_assign(1, 'Operation failed due to:'.$e->getMessage());
        }
        return to_assign();
    }
}
