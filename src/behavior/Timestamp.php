<?php

namespace src\behavior;

use src\helpers\Date;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class Timestamp extends Behavior
{
    public $create = ['created_at', 'updated_at'];
    public $update = ['updated_at'];
    public $format = 'datetime';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'insert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'update',
        ];
    }

    public function insert()
    {
        foreach ($this->create as $item) {
            $this->owner->{$item} = $this->format();
        }
    }

    public function update()
    {
        if ($this->update) {
            foreach ($this->update as $item) {
                $this->owner->{$item} = $this->format();
            }
        }
    }

    private function format()
    {
        switch ($this->format) {
            case 'datetime':
                $f = Date::datetime_now();
                break;
            case 'date':
                $f = Date::date_now();
                break;
            case 'year':
                $f = Date::year_now();
                break;
        }
        return $f;
    }
}