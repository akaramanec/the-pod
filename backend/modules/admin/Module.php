<?php

namespace backend\modules\admin;

class Module extends \yii\base\Module
{

    public $controllerNamespace = 'backend\modules\admin\controllers';

    public function init()
    {
        parent::init();
        $this->setLayoutPath('@backend/views/layouts');
    }
}
