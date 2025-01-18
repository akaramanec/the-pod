<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $id
 * @property string $name
 * @property string $uuid
 * @property string $data
 */
class TmpCategory extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'tmp_category';
    }

    public function rules()
    {
        return [
            [['name', 'uuid'], 'trim'],
            [['name', 'uuid', 'data'], 'required'],
            [['data'], 'safe'],
            [['name', 'uuid'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'uuid' => 'Uuid',
            'data' => 'Data',
        ];
    }
}
