<?php

namespace blog\modules\admin;

class Module extends \yii\base\Module
{

    public $controllerNamespace = 'blog\modules\admin\controllers';

    public function init()
    {
        parent::init();
        $this->setLayoutPath('@blog/views/layouts');
    }
}
