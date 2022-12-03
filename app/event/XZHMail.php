<?php
declare (strict_types = 1);

namespace app\event;

use app\facade\XZHMailApi;
use app\model\RecycleOrder;
use think\facade\Log;

class XZHMail
{
    public static function getLabels(){
        $where = [['status','=',1],['shipment_id','<>',''],['express_no','=',''],['create_time','>=',time() - 7 * 86400]];
        $list = RecycleOrder::where($where)->field(['id','shipment_id'])->limit(10)->order('create_time asc')->select();
        foreach ($list as $item){
            try {
                $info = XZHMailApi::getLabels($item['shipment_id']);
                if (!empty($info['shipment']['tracking_number'])){
                    $updateData = [
                        'express_no'=>$info['shipment']['tracking_number'],
                        'label_url'=>$info['shipment']['parcels'][0]['label_url'],
                        'update_time'=>time()
                    ];
                    RecycleOrder::where('id',$item['id'])->update($updateData);
                    sleep(1);
                }
            }catch (\Exception $e){
                Log::error('获取订单的物流跟踪号错误',['error'=>$e->getMessage(),'params'=>$item]);
            }
        }
    }
}
