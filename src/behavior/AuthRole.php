<?php

namespace src\behavior;

use backend\modules\admin\models\AuthAssignment;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class AuthRole extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'assignRole',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'assignRole',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteRole',
        ];
    }

    public function assignRole()
    {
        foreach ($this->owner->roles as $role) {
            $auth = new AuthAssignment();
            $auth->item_name = $role['item_name'];
            $auth->user_id = (string)$this->owner->id;
            $auth->save();
        }
    }

    public function deleteRole()
    {
        AuthAssignment::deleteAll(['user_id' => $this->owner->id]);
    }
}
