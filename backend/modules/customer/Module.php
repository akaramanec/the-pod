<?php

namespace backend\modules\customer;

class Module extends \yii\base\Module
{

    public $controllerNamespace = 'backend\modules\customer\controllers';

    public function init()
    {
        parent::init();
        $this->setLayoutPath('@backend/views/layouts');
    }
}
