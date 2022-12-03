<?php
 
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\BaseController;
use app\model\Voucher as VoucherModel;
use app\admin\validate\VoucherValidate;
use think\exception\ValidateException;
use think\facade\View;

class Voucher extends BaseController

{
	/**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new VoucherModel();
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
			!empty($param['keywords']) && $where[] = ['code|passwd', 'like', '%' . $param['keywords'] . '%'];
            $list = $this->model->getVoucherList($where,$param);
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
                validate(VoucherValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->addVoucher($param);
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
                validate(VoucherValidate::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
			
            $this->model->editVoucher($param);
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail = $this->model->getVoucherById($id);
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
		$detail = $this->model->getVoucherById($id);
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
        $this->model->delVoucherById($id);
   }

   public function upload()
   {
       $files = request()->file();
       try {
           validate(VoucherValidate::class)->scene(request()->action())->check($files);
       } catch (ValidateException $e) {
           return to_assign(1,$e->getMessage());
       }
       $file = $files['file'];
       // 日期前綴
       $dataPath = date('Ym');
       $md5 = $file->hash('md5');
       $filename = \think\facade\Filesystem::disk('local')->putFile($dataPath, $file, function () use ($md5) {
           return $md5;
       });
       $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(\think\facade\Filesystem::path($filename));
       //读取第一张表
       $worksheet = $spreadsheet->getSheet(0);
       //总行数
       $rowNum = $worksheet->getHighestRow();
       //总列数
       $colNum = $worksheet->getHighestColumn();

       $fieldMapping = ['code'=>'code','password'=>'passwd','denomination'=>'value','points_needed'=>'deduct_points','remark'=>'remark'];
       $insertData = [];
       for($i=1; $i <= $rowNum; $i++){
           $tmp = [];
           for($j='A'; $j <= $colNum; $j++){
               $tmp[$fieldMapping[$worksheet->getCell("$j"."1")->getValue()]] = $worksheet->getCell("$j$i")->getValue()??"";
           }
           if($i==1) continue;
           if(empty($tmp['code']) || empty($tmp['value']) || empty($tmp['deduct_points'])) continue;
           $insertData[] = $tmp;
       }

       VoucherModel::extra('IGNORE')->insertAll($insertData);
       return to_assign();
   }
}
