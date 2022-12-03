<?php


namespace app\common;


use PaypalPayoutsSDK\Core\PayPalHttpClient;
use PaypalPayoutsSDK\Core\SandboxEnvironment;
use PaypalPayoutsSDK\Payouts\PayoutsGetRequest;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;

class PayoutClient
{
    protected $client = null;
    protected $emailSubject = '';
    protected $emailMessage = '';

    /**
     * 架构方法 设置参数
     * @access public
     */
    public function __construct()
    {
        $config = get_system_config('paypal');
        $this->emailSubject = $config['email_subject'] ?? "";
        $this->emailMessage = $config['email_message'] ?? "";
        $environment = new SandboxEnvironment($config['client_id'], $config['client_secret']);
        $this->client = new PayPalHttpClient($environment);
    }

    protected function buildId($userId){
        return $userId."_".time();
    }

    public function payout($sendId,$email,$amount,$currency='GBP'){
        $request = new PayoutsPostRequest();
        $request->body = [
            'sender_batch_header' => [
                'sender_batch_id' => $sendId,
                'recipient_type' => 'EMAIL',
                'email_subject' => $this->emailSubject,
                'email_message' => $this->emailMessage
            ],
            'items' => [
                [
                    'sender_item_id' => $sendId.'_1',
                    'amount' => [
                        'currency' => $currency,
                        'value' => $amount
                    ],
                    'receiver' => $email
                ]
            ]
        ];
        $res = $this->client->execute($request);
        return $res->result;
    }

    public function getPayout($payoutBatchId){
        $request = new PayoutsGetRequest($payoutBatchId);
        $res = $this->client->execute($request);
        return $res->result;
    }
}