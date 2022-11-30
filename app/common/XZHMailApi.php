<?php


namespace app\common;


/**
 * 新智慧TMS API
 * Class XZHMailApi
 * @package app\common
 */
class XZHMailApi extends RemoteApi
{
    protected $baseUrl = 'http://mailpl.nextsls.com/api/v5/';
    protected $headers = ['Accept' => 'application/json','Content-Type' => 'application/json','Authorization' => "Bearer 6364facb69c1d66f3e7d859b6364facbde8cf8142"];
    protected $toUser = [
        'name'=>'刘先生','tel'=>'18888888888','address'=>'Amazonstrasse 1','city'=>'london','country'=>'DE','postcode'=>'47495'
    ];

    public function create($fromUser){
        $params = [
            'shipment' => [
                'service' => 'B2C-TEST',
                'parcel_count' => 1,
                'to_address' => [
                    'name' => $this->toUser['name'],
                    'tel' => $this->toUser['tel'],
                    'address_1' => $this->toUser['address'],
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
                    'country' => 'DE',
                    'postcode' => $fromUser['postcode']
                ],
                'parcels' => [
                    [
                        'number' => 1,
                        'client_weight' => 1,
                        'declarations' => [
                            [
                                'name_zh' => '电子烟',
                                'name_en' => 'electronic cigarette',
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