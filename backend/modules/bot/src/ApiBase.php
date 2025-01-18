<?php

namespace backend\modules\bot\src;

use backend\modules\bot\models\BotLogger;
use Yii;
use yii\helpers\Json;

class ApiBase
{
    protected $_url;
    protected $_key;
    protected $_pass;

    protected function curlJson($options = [])
    {
        $curl = curl_init($options['url']);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        if ($options['mode'] == 'post') {
            $json_data = Json::encode($options['data']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($curl, CURLOPT_POST, true);
        }
        if ($options['mode'] == 'get') {
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $output = curl_exec($curl);

        if ($output === false) {
            throw new \Exception(curl_error($curl));
        }
        curl_close($curl);
        return Json::decode($output);
    }

    protected function headers()
    {
        return [
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Content-Type: application/json; charset=UTF-8',
            'Authorization: Basic ' . base64_encode($this->_key . ':' . $this->_pass)
        ];
    }

    protected function post($url, $data)
    {
        return $this->curlJson([
            'headers' => $this->headers(),
            'mode' => 'post',
            'url' => $this->_url . $url,
            'data' => $data
        ]);
    }

    protected function get($url)
    {
        return $this->curlJson([
            'headers' => $this->headers(),
            'mode' => 'get',
            'url' => $this->_url . $url,
        ]);
    }
}
