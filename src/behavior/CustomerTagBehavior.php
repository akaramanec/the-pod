<?php


namespace src\behavior;

use backend\modules\customer\models\CustomerTagLink;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class CustomerTagBehavior extends Behavior
{
    public $tags = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'save',
            ActiveRecord::EVENT_AFTER_UPDATE => 'save'
        ];
    }

    public function save()
    {
        CustomerTagLink::deleteAll(['customer_id' => $this->owner->id]);
        if ($this->owner->tags) {
            foreach ($this->owner->tags as $tag_id) {
                $t = new CustomerTagLink();
                $t->customer_id = $this->owner->id;
                $t->tag_id = $tag_id;
                $t->save();
            }
        }
    }
}
