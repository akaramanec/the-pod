<?php

namespace backend\widgets;

use yii\base\Widget;

class StatusEvent extends Widget
{
    public $event;

    public function run()
    {
        return $this->render('status-event', [
            'event' => $this->event,
        ]);
    }
}
