<?php

namespace src\behavior;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class JsonMore extends Behavior
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
        foreach ($this->fields as $field) {
            $a[$field] = $this->owner->{$field};
        }
        $this->owner->more = $a;
    }
}