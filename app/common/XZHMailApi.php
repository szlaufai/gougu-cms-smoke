<?php


namespace app\common;


/**
 * 新智慧TMS API
 * Class XZHMailApi
 * @package app\common
 */
class XZHMailApi extends RemoteApi
{
    protected $config = [];
    protected $service = '';
    protected $baseUrl = 'http://mailpl.nextsls.com/api/v5/';
    protected $headers = ['Accept' => 'application/json','Content-Type' => 'application/json','Authorization' => ""];
    protected $toUser = [
        'name'=>'','tel'=>'','address'=>'','city'=>'','country'=>'','postcode'=>''
    ];
    protected $goodsName = '';

    public function __construct()
    {
        $this->config = get_system_config('nextsls');
        $this->headers['Authorization'] = "Bearer " . $this->config['token'];
        $this->service = $this->config['service'];
        $this->goodsName = $this->config['goods_name'];
        $this->toUser['name'] = $this->config['consignee'];
        $this->toUser['tel'] = $this->config['telephone'];
        $this->toUser['address_1'] = $this->config['address1'];
        $this->toUser['address_2'] = $this->config['address2'];
        $this->toUser['city'] = $this->config['city'];
        $this->toUser['country'] = $this->config['country_code'];
        $this->toUser['postcode'] = $this->config['postcode'];
    }

    public function create($fromUser){
        $params = [
            'shipment' => [
                'service' => $this->service,
                'parcel_count' => 1,
                'to_address' => [
                    'name' => $this->toUser['name'],
                    'tel' => $this->toUser['tel'],
                    'address_1' => $this->toUser['address_1'],
                    'address_2' => $this->toUser['address_2'],
                    'city' => $this->toUser['city'],
                    'country' => $this->toUser['country'],
                    'postcode' => $this->toUser['postcode']
                ],
                'from_address' => [
                    'name' => $fromUser['name'],
                    'company' => $fromUser['company'] ?? "",
                    'tel' => $fromUser['mobile']?? "",
                    'address_1' => $fromUser['address'],
                    'city' => $fromUser['city'],
                    'country' => $fromUser['country'],
                    'postcode' => $fromUser['postcode']
                ],
                'parcels' => [
                    [
                        'number' => 1,
                        'client_weight' => 30,
                        'client_length' => 20,
                        'client_width' => 20,
                        'client_height' => 20,
                        'declarations' => [
                            [
                                'name_zh' => $this->goodsName,
                                'name_en' => $this->goodsName,
                                'unit_value' => 1
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $data = $this->post('shipment/create',$params);
        if($data['status'] != 1){
            throw new \Exception($data['info'], 1);
        }
        return $data['data'];
    }

    public function getLabels($shipmentId){
        $params = [
            "shipment" => [
                "shipment_id" => $shipmentId
            ]
        ];
        $data = $this->post('shipment/get_labels',$params);
        if($data['status'] != 1){
            throw new \Exception($data['info'], 1);
        }
        return $data['data'];
    }

    public function getTrackingRoute($expressNo){
        $params = [
            "shipment" => [
                "tracking_number" => $expressNo,
                "language" => "en"
            ]
        ];
        $data = $this->post('shipment/get_tracking',$params);
        if($data['status'] != 1){
            throw new \Exception($data['info'], 1);
        }
        return $data['data'];
    }

    public function cancel($shipmentId){
        $params = [
            "shipment" => [
                "shipment_id" => $shipmentId
            ]
        ];
        $data = $this->post('shipment/void',$params);
        if($data['status'] != 1){
            throw new \Exception($data['info'], 1);
        }
        return $data['data'];
    }
}