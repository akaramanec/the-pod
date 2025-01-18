<?php

namespace src\behavior;

use src\helpers\Common;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class CapitalLetters extends Behavior
{
    public $fields = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'save',
            ActiveRecord::EVENT_BEFORE_INSERT => 'save',
        ];
    }

    public function save()
    {
        foreach ($this->fields as $f) {
            $this->owner->{$f} = Common::capitalLetters($this->owner->{$f});
        }
    }


}