<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
namespace app\admin\model;
use Stripe\Exception\ApiErrorException;
use think\db\exception\DbException;
use think\facade\Log;
use think\model;
class DonateRecord extends Model
{
    public $typeEnum = [
        '1' => 'stripe',
    ];

    public $paymentStatusEnum = [
        '-1' => '支付失败',
        '0' => '待支付',
        '1' => '支付成功',
    ];

    public function fillTypeLabel(&$rows,$field='type'){
        foreach ($rows as &$row){
            $row['type_label'] = $this->typeEnum[$row[$field]];
        }
    }

    public function fillStatusLabel(&$rows,$field='payment_status'){
        foreach ($rows as &$row){
            $row['payment_status_label'] = $this->paymentStatusEnum[$row[$field]];
        }
    }

    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function getDonateRecordList($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app . page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        $list = $this->where($where)->field('id,type,third_payment_id,first_name,last_name,amount,mobile,email,country,province,city,address,payment_status,donate_ip,create_time')->order($order)->paginate($rows, false, ['query' => $param]);
        $this->fillTypeLabel($list);
        $this->fillStatusLabel($list);
		return $list;
    }

    /**
    * 添加数据
    * @param $param
    */
    public function addDonateRecord($param)
    {
		$insertId = 0;
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
    public function editDonateRecord($param)
    {
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
    public function getDonateRecordById($id)
    {
        $info = $this->where('id', $id)->select();
        $this->fillStatusLabel($info);
        $this->fillTypeLabel($info);
		return $info[0];
    }

    /**
    * 删除信息
    * @param $id
    * @return array
    */
    public function delDonateRecordById($id,$type=0)
    {
		if($type==0){
			//逻辑删除
			try {
				$param['delete_time'] = time();
				$this->where('id', $id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				$this->where('id', $id)->delete();
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }

    public function genRecord($param,$secretKey,$ip){
        $this->startTrans();
        try {
            $stripe = new \Stripe\StripeClient($secretKey);
            $ret = $stripe->paymentIntents->create(
                ['amount' => $param['amount'] * 100, 'currency' => 'gbp', 'automatic_payment_methods' => ['enabled'=>true]]
            );
            $insertData = [
                'type' => 1,
                'third_payment_id' => $ret['id'],
                'amount' => $param['amount'],
                'email' => $param['email'] ?? "",
                'payment_status' => 0,
                'donate_ip' => $ip ?? 0,
                'first_name' => $param['first_name'] ?? "",
                'last_name' => $param['last_name'] ?? "",
                'mobile' => $param['mobile'] ?? "",
                'country' => $param['country'] ?? "",
                'province' => $param['province'] ?? "",
                'city' => $param['city'] ?? "",
                'address' => $param['address'] ?? "",
                'create_time' => time()
            ];
            $this->insert($insertData);
            $this->commit();
            return $ret->client_secret;
        } catch (ApiErrorException $e){
            $this->rollback();
            Log::error('请求stripeAPI异常',['error'=>$e->getMessage()]);
            return false;
        } catch(DbException $e) {
            $this->rollback();
            Log::error('数据库操作异常',['error'=>$e->getMessage()]);
            return false;
        }
    }

    public function changePaymentStatus($thirdPaymentId,$status){
        $update = [
            'payment_status' => $status,
            'update_time' => time()
        ];
        $this->insert($insertData);
    }
}

