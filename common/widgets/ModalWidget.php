<?php

namespace common\widgets;

use yii\base\Widget;

class ModalWidget extends Widget
{
    public $id;
    public $title = '';

    public function run()
    {
        return $this->render('modal', [
            'id' => $this->id,
            'title' => $this->title
        ]);
    }
}
