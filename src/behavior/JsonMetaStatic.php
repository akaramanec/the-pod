<?php

namespace src\behavior;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class JsonMetaStatic extends Behavior
{
    public $meta_title;
    public $meta_description;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'save',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'save',
            ActiveRecord::EVENT_AFTER_FIND => 'find',
        ];
    }

    public function find()
    {
        $this->meta_title = $this->owner->meta['title'];
        $this->meta_description = $this->owner->meta['description'];
    }

    public function save()
    {

        $meta = [];
        $meta['title'] = $this->owner->meta_title;
        $meta['description'] = $this->owner->meta_description;
        $this->owner->meta = $meta;
    }

}
