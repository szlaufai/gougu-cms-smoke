<?php


namespace app\common;



class RemoteApi
{
    protected $baseUrl = '';
    protected $headers = [];

    public function get($url,$query=[],$headers=[]){
        $headers = array_merge($headers,$this->headers);
        $client = new \GuzzleHttp\Client(['base_uri' => $this->baseUrl,'headers'=>$headers]);
        $response = $client->request('GET',$url,['query'=>$query]);
        $code = $response->getStatusCode();
        if($code != 200){
            throw new \Exception($response->getBody(), $code);
        }
        $bodyJson = $response->getBody()->__toString();
        return json_decode($bodyJson, true);
    }

    public function post($url,$body=[],$headers=[]){
        $headers = array_merge($headers,$this->headers);
        $client = new \GuzzleHttp\Client(['base_uri' => $this->baseUrl,'headers'=>$headers]);
        $response = $client->request('POST',$url,['json'=>$body]);
        $code = $response->getStatusCode();
        if($code != 200){
            throw new \Exception($response->getBody(), $code);
        }
        $bodyJson = $response->getBody()->__toString();
        return json_decode($bodyJson, true);
    }
}