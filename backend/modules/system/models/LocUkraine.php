<?php

namespace backend\modules\system\models;

use Yii;
use yii\helpers\ArrayHelper;

class LocUkraine extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'loc_ukraine';
    }

    public function rules()
    {
        return [
            [['parent_id', 'code', 'level'], 'integer'],
            [['code', 'name', 'level'], 'required'],
            [['name', 'new_name'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 10],
            [['code'], 'unique'],
            [['new_name', 'name', 'type'], 'trim'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => LocUkraine::className(), 'targetAttribute' => ['parent_id' => 'ukraine_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ukraine_id' => 'Ukraine ID',
            'parent_id' => 'Parent ID',
            'code' => 'Code',
            'name' => 'Name',
            'type' => 'Type',
            'level' => 'Level',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(LocUkraine::className(), ['ukraine_id' => 'parent_id']);
    }

    public function getLocUkraines()
    {
        return $this->hasMany(LocUkraine::className(), ['parent_id' => 'ukraine_id']);
    }

    public static function l($parent_id = null)
    {
        if ($parent_id != null) {
            $q = Yii::$app->db->createCommand("SELECT ukraine_id, new_name
        FROM loc_ukraine
        WHERE parent_id=:parent_id
        ORDER BY ukraine_id")
                ->bindValue(':parent_id', $parent_id)->queryAll();
        } else {
            $q = Yii::$app->db->createCommand("SELECT ukraine_id, new_name
        FROM loc_ukraine
        WHERE parent_id IS NULL
        ORDER BY ukraine_id")->queryAll();
        }
        return ArrayHelper::map($q, 'ukraine_id', 'new_name');
    }
}
