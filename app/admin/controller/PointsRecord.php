<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
 
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\model\PointsRecord as PointsRecordModel;
use app\admin\validate\PointsRecordValidate;
use think\exception\ValidateException;
use think\facade\View;

class PointsRecord extends BaseController

{
	/**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new PointsRecordModel();
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
			
            $list = $this->model->getPointsRecordList($where,$param);
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
                validate(PointsRecordValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->addPointsRecord($param);
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
                validate(PointsRecordValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->editPointsRecord($param);
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail = $this->model->getPointsRecordById($id);
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
		$detail = $this->model->getPointsRecordById($id);
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
    */
    public function del()
    {
        $param = get_params();

        $this->model->delPointsRecordById($param['id']);
   }

    /**
     * 审核通过
     */
    public function approved()
    {
        $param = get_params();
        $this->model->approved($param['id']);
    }
}
