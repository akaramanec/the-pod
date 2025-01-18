<?php

namespace backend\modules\admin\models;

use Yii;

class AuthAssignment extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'auth_assignment';
    }

    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [['item_name', 'user_id'], 'unique', 'targetAttribute' => ['item_name', 'user_id']],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    public static function save_role($role = [], $user_id)
    {
        foreach ($role as $item) {
            $auth = new self();
            $auth->item_name = $item;
            $auth->user_id = (string)$user_id;
            if ($auth->save()) {
                return true;
            }
        }
    }

    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }
}
