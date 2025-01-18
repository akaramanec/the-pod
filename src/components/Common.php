<?php

namespace src\components;

use backend\modules\system\models\Setting;
use src\helpers\Date;
use yii\base\BaseObject;

class Common extends BaseObject
{
    public $settingNp = [];
    private $_error;
    public $datetimeNow;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->settingNp = Setting::listValue('new-mail');
        $this->datetimeNow = Date::datetime_now();
    }

    public function getError()
    {
        return $this->_error;
    }

    public function setError($error)
    {
        $this->_error = $error;
    }


}
