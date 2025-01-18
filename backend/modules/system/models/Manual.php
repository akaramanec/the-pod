<?php

namespace backend\modules\system\models;

use Yii;

class Manual extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'manual';
    }

    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }
}
