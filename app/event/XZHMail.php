<?php
declare (strict_types = 1);

namespace app\event;

use app\facade\XZHMailApi;
use app\model\RecycleOrder;
use think\facade\Log;

class XZHMail
{
    public static function getLabels(){
        $where = [['shipment_status','=',1],['status','=',1],['shipment_id','<>',''],['express_no|label_url','=',''],['create_time','>=',time() - 7 * 86400]];
        $list = RecycleOrder::where($where)->field(['id','shipment_id'])->limit(0,10)->order('create_time asc')->select();
        foreach ($list as $item){
            try {
                $info = XZHMailApi::getLabels($item['shipment_id']);
                if (!empty($info['shipment']['tracking_number'])){
                    $labelUrl = self::downloadLabel($info['shipment']['parcels'][0]['label_url'],$info['shipment']['tracking_number']);
                    $updateData = [
                        'express_no'=>$info['shipment']['tracking_number'],
                        'label_url'=> $labelUrl,
                        'update_time'=>time()
                    ];
                    RecycleOrder::where('id',$item['id'])->update($updateData);
                    sleep(3);
                }
            }catch (\Exception $e){
                RecycleOrder::where('id',$item['id'])->update(['shipment_status' => 0,'update_time' => time()]);
                Log::error('获取订单的物流跟踪号错误'.json_encode(['error'=>$e->getMessage(),'params'=>$item]));
            }
        }
    }

    public static function downloadLabel($url,$fileName){
        $fp_input = fopen($url, 'r');
        if (!$fp_input){
            Log::error('下载物流标签异常'.json_encode(['tracking_number'=>$fileName]));
            return '';
        }
        $baseDir = public_path();
        $basePath = 'storage'.DIRECTORY_SEPARATOR.'labels'.DIRECTORY_SEPARATOR;
        $file = $fileName.'.pdf';
        if (!is_dir($baseDir.$basePath)) {
            mkdir($baseDir.$basePath, 0755, true);
        }
        file_put_contents($baseDir.$basePath.$file, $fp_input);
        fclose($fp_input);
        return $basePath.$file;
    }
}
