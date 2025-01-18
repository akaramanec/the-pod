<?php

namespace src\components;

use backend\modules\system\models\Liqpay as LiqPayModel;
use src\api\LiqPay;
use yii\base\BaseObject;

class LiqPayComponent extends BaseObject
{
    public $public_key;
    public $private_key;
    public $liqPay;

    public function __construct($config = [])
    {
        $liqPay = LiqPayModel::find()->where(['id' => 1])->limit(1)->one();
        if ($liqPay->status == LiqPayModel::STATUS_PROD) {
            $this->public_key = $liqPay->public_key;
            $this->private_key = $liqPay->private_key;
        }
        if ($liqPay->status == LiqPayModel::STATUS_TEST) {
            $this->public_key = $liqPay->test_public_key;
            $this->private_key = $liqPay->test_private_key;
        }
        
        $this->liqPay = new LiqPay($this->public_key, $this->private_key);
        parent::__construct($config);
    }
}
