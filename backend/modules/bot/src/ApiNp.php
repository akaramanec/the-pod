<?php

namespace backend\modules\bot\src;

use Yii;
use yii\helpers\Json;

/**
 * https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/55702571a0fe4f0b64838913
 * https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/56261f14a0fe4f1e503fe187
 * https://devcenter.novaposhta.ua/docs/services/556d7280a0fe4f08e8f7ce40/operations/574d90f4a0fe4f1150e501f4
 * https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702cbba0fe4f0cf4fc53ee    Актуальные статусы трекинга
 * https://devcenter.novaposhta.ua/blog/%D0%BF%D0%BE%D1%81%D1%82%D1%80%D0%BE%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%B0%D0%BE%D0%B2-%D0%BA-api
 * 'CategoryOfWarehouse' => 'Postomat' 'CategoryOfWarehouse' => 'Branch'
 */
class ApiNp
{

    protected $_url = 'https://api.novaposhta.ua/v2.0/json/';
    protected $_key;

    public function __construct()
    {
        $this->_key = Yii::$app->common->settingNp['key'];
    }

    protected function curlJson($options = [])
    {
        $curl = curl_init($this->_url);
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
        return json_decode($output);
    }

    private function headers()
    {
        return [
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Host: api.novaposhta.ua',
            'Content-Type: application/json; charset=UTF-8',
        ];
    }

    public function post($data)
    {
        $data = $data + ['apiKey' => $this->_key];
        return $this->curlJson([
            'headers' => $this->headers(),
            'mode' => 'post',
            'data' => $data
        ]);
    }

    public function get()
    {
        return $this->curlJson([
            'headers' => $this->headers(),
            'mode' => 'get',
        ]);
    }

    public function test()
    {
        $data = [
            'apiKey' => $this->_key,
            'modelName' => 'Common',
            'calledMethod' => 'getServiceTypes'
        ];
        return $this->post($data);
    }


}
