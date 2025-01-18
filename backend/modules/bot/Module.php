<?php

namespace backend\modules\bot;


class Module extends \yii\base\Module
{

    public $controllerNamespace = 'backend\modules\bot\controllers';

    public function init()
    {
        parent::init();
        $this->setLayoutPath('@backend/views/layouts');
    }
}
