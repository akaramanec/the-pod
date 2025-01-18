<?php

namespace src\behavior;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class JsonMeta extends Behavior
{
    public $meta_title;
    public $meta_description;
    public $meta_keywords;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'save',
            ActiveRecord::EVENT_BEFORE_INSERT => 'save',
            ActiveRecord::EVENT_AFTER_FIND => 'find',
        ];
    }

    public function find()
    {
        $this->meta_title = $this->owner->meta['title'];
        $this->meta_description = $this->owner->meta['description'];
        $this->meta_keywords = $this->owner->meta['keywords'];
    }

    public function save()
    {
        $m = [];
        $m['title'] = $this->owner->meta_title;
        $m['description'] = $this->owner->meta_description;
        $m['keywords'] = $this->owner->meta_keywords;
        $this->owner->meta = $m;
    }
}