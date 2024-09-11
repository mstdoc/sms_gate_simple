<?php

namespace Mstdoc\SmsGateSimple;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Mstdoc\SmsGateSimple\Exceptions\SmsGateException;

class SmsGate {
    protected $host;
    protected $url = '/api/1.0/message/create';

    public function __construct($host, $url = null) {
        $this->host = $host;

        if ( !is_null($url) ){
            $this->url = $url;
        }
    }

    /**
     * @param Message $sms
     * @return $this
     * @throws SmsGateException
     */
    public function send( Message $sms )
    {
        $request = $sms->to_request_arr();
        $this->send_handler($request);

        return $this;
    }

    /**
     * @param $request_string
     * @return $this
     * @throws SmsGateException
     */
    protected function send_handler($request_string){
        try {
            $client = new Client(['base_uri' => $this->host, 'verify' => false]);
            $client->post($this->url, ['json' => $request_string]);
        }
        catch (GuzzleException $e){
            throw new SmsGateException('#1 Error sending sms. ' . $e->getMessage());
        }
        catch (Exception $e){
            throw new SmsGateException('#2 Error sending sms. ' . $e->getMessage());
        }

        return $this;
    }
}