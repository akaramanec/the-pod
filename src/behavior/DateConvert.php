<?php

namespace src\behavior;

use src\helpers\Date;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class DateConvert extends Behavior
{
    public $fields = [];
    public $format = 'datetime';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'get',
            ActiveRecord::EVENT_BEFORE_INSERT => 'save',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'save',
        ];
    }

    public function get()
    {
        foreach ($this->fields as $item) {
            $this->owner->{$item} = $this->getFormat($this->owner->{$item});
        }
    }

    public function save()
    {
        foreach ($this->fields as $item) {
            $this->owner->{$item} = $this->setFormat($this->owner->{$item});
        }
    }

    private function setFormat($d)
    {
        switch ($this->format) {
            case 'datetime':
                $f = Date::datetime_converter($d);
                break;
            case 'date':
                $f = Date::date_converter($d);
                break;
        }
        return $f;
    }

    private function getFormat($d)
    {
        switch ($this->format) {
            case 'datetime':
                $f = Date::format_datetime($d);
                break;
            case 'date':
                $f = Date::format_date($d);
                break;
        }
        return $f;
    }
}
