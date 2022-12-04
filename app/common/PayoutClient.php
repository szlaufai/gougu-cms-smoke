<?php


namespace app\common;


use PaypalPayoutsSDK\Core\PayPalHttpClient;
use PaypalPayoutsSDK\Core\SandboxEnvironment;
use PaypalPayoutsSDK\Payouts\PayoutsGetRequest;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;
use think\facade\Log;

class PayoutClient
{
    protected $client = null;
    protected $emailSubject = '';
    protected $emailMessage = '';
    protected $webhookId = '';

    /**
     * 架构方法 设置参数
     * @access public
     */
    public function __construct()
    {
        $config = get_system_config('paypal');
        $this->emailSubject = $config['email_subject'] ?? "";
        $this->emailMessage = $config['email_message'] ?? "";
        $this->webhookId = $config['webhook_id'] ?? "";
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

    public function verifyWebhook($headers,$payload){
        $request = new PaypalWebhookVerifyRequest();
        $request->body = [
            'webhook_id' => $this->webhookId,
            'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'],
            'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'],
            'cert_url' => $headers['PAYPAL-CERT-URL'],
            'auth_algo' => $headers['PAYPAL-AUTH-ALGO'],
            'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'],
            'webhook_event' => $payload,
        ];
        $res = $this->client->execute($request);
        return $res->result->verification_status == 'SUCCESS';
    }
}