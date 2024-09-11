<?php

namespace Mstdoc\SmsGateSimple;


use Mstdoc\SmsGateSimple\Exceptions\MessageException;

class Message {
    protected $id       = null;
    protected $phone    = null;
    protected $text     = null;
    protected $template = null;
    protected $params   = [];

    /**
     * @param $phone
     * @param $text
     * @param $template
     * @throws MessageException
     */
    public function __construct( $phone, $text = null, $template = null ){
        $this->generate_id();
        $this->set_phone($phone);

        if (!is_null($template)){
            $this->set_template($template);
        }

        if (!is_null($text)){
            $this->set_text($text);
        }
    }

    public function is_templated(){
        if (is_null($this->template)){
            return false;
        }

        return true;
    }

    /**
     * @param $phone
     * @return $this
     * @throws MessageException
     */
    protected function set_phone( $phone ){
        $phone_filtered = preg_replace("/\D/", '', $phone);

        if( $phone_filtered != $phone ){
            throw new MessageException(sprintf('Only digits allowed in phone number. Got value: "%s"', $phone));
        }

        if( !preg_match('/^\d{11}$/', $phone_filtered) ){
            throw new MessageException(sprintf('Wrong phone num format. Got value: "%s"', $phone));
        }

        $this->phone = $phone_filtered;

        return $this;
    }

    public function set_template($value){
        $this->template  = $value;
    }

    public function set_text($value){
        $this->text  = $value;
    }

    public function get_template(){
        return $this->template;
    }

    public function get_id(){
        return $this->id;
    }

    public function get_phone(){
        return $this->phone;
    }

    public function get_text(){
        return $this->text;
    }

    protected function generate_id(){
        $this->id = 'msg'. date('YmdHis') .'_'. mt_rand();

        return $this;
    }

    public function add_param($name, $value){
        $this->params[$name] = $value;
    }

    public function get_params(){
        return $this->params;
    }

    /**
     * @return array
     */
    public function to_request_arr(){
        $arr = [
            'srcMessageId' => $this->get_id(),
            'address'      => $this->get_phone(),
            'priorityFlag' => 6,
            'message'      => $this->get_text(),
        ];

        if ($this->is_templated()){
            $arr['templateCode'] = $this->get_template();
            $arr['params']       = $this->get_params();
        }


        return $arr;
    }
}