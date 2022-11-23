<?php
 
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\model\DonateRecord as DonateRecordModel;
use app\admin\validate\DonateRecordValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class DonateRecord extends BaseController

{
	/**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new DonateRecordModel();
		$this->uid = get_login_admin('id');
    }
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [['status','<>','-1']];
            !empty($param['keywords']) && $where[] = ['email', 'like', '%' . $param['keywords'] . '%'];
            //按时间检索
            $start_time = !empty($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = !empty($param['end_time']) ? strtotime(urldecode($param['end_time'])) + 86400 : 0;

            if ($start_time > 0 && $end_time > 0) {
                if ($start_time === $end_time) {
                    $where[] = ["create_time", '=', $start_time];
                } else {
                    $where[] = ["create_time", '>=', $start_time];
                    $where[] = ["create_time", '<=', $end_time];
                }
            } elseif ($start_time > 0 && $end_time == 0) {
                $where[] = ["create_time", '>=', $start_time];
            } elseif ($start_time == 0 && $end_time > 0) {
                $where[] = ["create_time", '<=', $end_time];
            }
            $list = $this->model->getDonateRecordList($where,$param);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }

    /**
    * 添加
    */
    public function add()
    {
        if (request()->isAjax()) {		
			$param = get_params();	
			
            // 检验完整性
            try {
                validate(DonateRecordValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->addDonateRecord($param);
        }else{
			return view();
		}
    }
	

    /**
    * 编辑
    */
    public function edit()
    {
		$param = get_params();
		
        if (request()->isAjax()) {			
            // 检验完整性
            try {
                validate(DonateRecordValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->editDonateRecord($param);
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail = $this->model->getDonateRecordById($id);
			if (!empty($detail)) {
				View::assign('detail', $detail);
				return view();
			}
			else{
				throw new \think\exception\HttpException(404, '找不到页面');
			}			
		}
    }


    /**
    * 查看信息
    */
    public function read()
    {
        $param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$detail = $this->model->getDonateRecordById($id);
		if (!empty($detail)) {
			View::assign('detail', $detail);
			return view();
		}
		else{
			throw new \think\exception\HttpException(404, '找不到页面');
		}
    }

    /**
    * 删除
	* type=0,逻辑删除，默认
	* type=1,物理删除
    */
    public function del()
    {
        $param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$type = isset($param['type']) ? $param['type'] : 0;

        $this->model->delDonateRecordById($id,$type);
   }
}
