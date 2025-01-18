<?php

namespace backend\modules\notification;

/**
 * notification module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $layout = 'base';

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backend\modules\notification\controllers';

    /**
     * @var string
     */
    public $defaultRoute = 'notification';


    public function init()
    {
        parent::init();
        $this->setLayoutPath('@backend/views/layouts');
    }
}
