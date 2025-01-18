<?php

namespace backend\modules\bot\src;

use backend\modules\bot\models\Logger;

class ActionReflection
{
    private $class;
    private $method;

    public function actionTelegram($action)
    {
        $path = 'backend\\modules\\bot\\telegram\\';
        $action = substr($action, 1);
        $action = explode('_', $action);
        if (count($action) == 1) {
            $this->class = $path . 'TCommon';
            $this->method = $action[0];
        } else {
            $this->class = $path . $action[0];
            $this->method = $action[1];
        }
        $this->route();
    }

    public function actionViber($action)
    {
        $path = 'backend\\modules\\bot\\viber\\';
        $action = explode('_', $action);
        $this->class = $path . $action[0];
        $this->method = $action[1];
        $this->route();
    }

    private function route()
    {
        try {
            $object = new $this->class;
            if (method_exists($object, $this->method)) {
                return $object->{$this->method}();
            }
        } catch (\Exception $e) {
            Logger::commit([$e->getMessage(), $e->getTraceAsString()], __METHOD__);
            exit(__METHOD__);
        }
    }
}