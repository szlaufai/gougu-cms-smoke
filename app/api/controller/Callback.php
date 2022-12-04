<?php


namespace app\api\controller;


use app\admin\model\DonateRecord;
use app\api\BaseController;
use app\facade\PayoutClient;
use app\model\PayoutStatus;
use Baiy\ThinkAsync\Facade\Async;
use think\facade\Log;

class Callback extends BaseController
{
    public function stripe(){
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $payload = @file_get_contents('php://input');
        $config = get_system_config('stripe');
        if (empty($config)){
            Log::error('未配置stripe',['config_name'=>'stripe']);
            $this->apiError('System error, please try later.');
        }
        $endpoint_secret = $config['endpoint_secret'];

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            Log::error('stripe回调参数错误',['error'=>$e->getMessage()]);
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('stripe回调签名错误',['error'=>$e->getMessage()]);
            http_response_code(400);
            exit();
        }

        $status = 0;
        $needEmail = false;
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $status = '1';
                $needEmail = true;
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $status = '-1';
                break;
            default:
                Log::error('stripe回调异常，未知的事件类型:'.$event->type);
                $paymentIntent = [];
        }
        empty($paymentIntent) && $this->apiSuccess();
        $record = DonateRecord::where([['third_payment_id','=',$paymentIntent['id']],['type','=',1]])->find();
        $record->payment_status = $status;
        $record->update_time = time();
        $record->amount = $paymentIntent['amount'];
        $record->save();
        if ($needEmail && !empty($record->email)){
            Async::trigger('donate_succeed',$record->email,$record->amount / 100);
        }

        $this->apiSuccess();
    }

    public function paypal(){
        $params = get_params();
        $headers = $this->getAllHeaders();
        $payoutStatus = '';
        if (PayoutClient::verifyWebhook($headers,$params)){
            $eventType = $params['event_type'];
            switch ($eventType) {
                case 'PAYMENT.PAYOUTSBATCH.SUCCESS':
                    $payoutStatus = 'SUCCESS';
                    break;
                case 'PAYMENT.PAYOUTSBATCH.DENIED':
                    $payoutStatus = 'DENIED';
                    break;
                case 'PAYMENT.PAYOUTSBATCH.PROCESSING':
                    $payoutStatus = 'PROCESSING';
                    break;
            }
            empty($payoutStatus) && $this->apiSuccess();
            $where = [['payout_batch_id','=',$params['resource']['batch_header']['payout_batch_id']]];
            $updateData = ['batch_status'=>$payoutStatus,'update_time'=>time()];
            PayoutStatus::where($where)->update($updateData);
        }
        $this->apiSuccess();
    }
}