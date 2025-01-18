<?php

namespace backend\modules\shop;

class Module extends \yii\base\Module
{
    public $layout = 'base';
    public $controllerNamespace = 'backend\modules\shop\controllers';

    public function init()
    {
        parent::init();
        $this->setLayoutPath('@backend/views/layouts');
    }
}
