<?php

namespace src\services\data;

use backend\modules\customer\models\Customer;
use src\helpers\Common;
use src\helpers\CustomerHelper;
use src\helpers\Date;
use Yii;

class CustomerData
{
    public $model;
    public $data = [];

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->data();
    }

    private function data()
    {
        return $this->data = [
            'id' => $this->customer->id,
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'phone' => $this->customer->phone,
            'email' => $this->customer->email,
        ];
    }



}
