<?php

namespace src\services;

use backend\modules\customer\models\Customer;
use backend\modules\customer\models\search\CustomerStatisticSearch;
use backend\modules\media\models\GetImg;
use Yii;
use yii\bootstrap4\Html;


class CustomerStatistic
{
    public $models = [];
    private $_models;
    private $_item;
    private $_totalLesson;

    public function __construct($models)
    {
        $this->_models = $models;
        $this->_totalLesson = Yii::$app->db->createCommand("SELECT COUNT(*) FROM lesson")->queryScalar();
        $this->getModels();
    }

    private function getModels()
    {
        foreach ($this->_models as $item) {
            if ($item->group_id === null) {
                $this->_item = $item;
                $customer = new CustomerStatisticSearch();
                $customer->id = $this->_item->id;
                $customer->first_name = $this->_item->first_name . ' ' . $this->_item->last_name;
                $customer->status_homework = $this->statusHomework();
                $customer->platform = $this->platform();
                $customer->name_course = $this->nameCourse();

                $this->models[] = $customer;
            };
        }
    }

    public function nameCourse()
    {
        return $this->_item->course->name;
    }

    public function platform()
    {
        return GetImg::iconBot($this->_item->bot->platform, '31x31');
    }

    private function statusHomework()
    {
        return count($this->_item->homework) . '/' . $this->_totalLesson;
    }
}
