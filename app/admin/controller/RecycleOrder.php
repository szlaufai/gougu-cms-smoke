<?php
 
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\model\RecycleOrder as RecycleOrderModel;
use app\admin\validate\RecycleOrderValidate;
use think\exception\ValidateException;
use think\facade\View;

class RecycleOrder extends BaseController

{
	/**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new RecycleOrderModel();
		$this->uid = get_login_admin('id');
    }
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
			$param = get_params();
            $list = $this->model->getRecycleOrderList($param);
            return table_assign(0, '', $list);
        }
        else{
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
                validate(RecycleOrderValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->editRecycleOrderByRoot($param);
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail = $this->model->getRecycleOrderById($id);
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
     * 编辑
     */
    public function editByAdmin()
    {
        $param = get_params();

        if (request()->isAjax()) {
            // 检验完整性
            try {
                validate(RecycleOrderValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }

            $this->model->editRecycleOrderByRoot($param);
        }else{
            $id = isset($param['id']) ? $param['id'] : 0;
            $detail = $this->model->getRecycleOrderById($id);
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
		$detail = $this->model->getRecycleOrderById($id);
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
        $this->model->delRecycleOrderById($param['id']);
    }

    public function receipt()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (isset($param['id'])){
                try {
                    validate(RecycleOrderValidate::class)->scene(request()->action())->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $this->model->receiptConfirm($param);
            }else{
                $info = $this->model->getRecycleOrderByExpress($param['expresss_no']);
                return to_assign(0,'',$info);
            }
        }else{
            return view();
        }
    }
}
