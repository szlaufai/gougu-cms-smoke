<?php
declare (strict_types = 1);

namespace app\event;

use app\facade\PayoutClient;
use app\model\PayoutStatus;
use PayPalHttp\HttpException;
use think\db\exception\DbException;
use think\facade\Log;

class Payout
{
    public static function checkStatus(){
        $where = [['status','=',1],['batch_status','=','PENDING'],['create_time','>=',time() - 30 * 86400]];
        $list = PayoutStatus::where($where)->field(['id','payout_batch_id'])->limit(10)->order('create_time asc')->select();
        foreach ($list as $item){
            try {
                $info = PayoutClient::getPayout($item['payout_batch_id']);
                $status = $info->batch_header->batch_status;
                if ($status != 'PENDING'){
                    $updateData = ['batch_status'=>$status, 'update_time'=>time()];
                    PayoutStatus::where('id',$item['id'])->update($updateData);
                    sleep(1);
                }
            }catch (HttpException $e){
                Log::error('查询payout状态异常'.$e->getMessage());
            }catch (DbException $e){
                Log::error('写入payout状态异常'.json_encode(['error'=>$e->getMessage(),'params'=>$item]));
            }
        }
    }
}
