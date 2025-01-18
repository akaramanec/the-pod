<?php

namespace backend\modules\media\models;

use Yii;

/**
 * @property int $id
 * @property int $entity_id
 * @property int $entity
 * @property string $file
 * @property string $name
 * @property int $sort
 */
class Files extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'media_file';
    }

    public function rules()
    {
        return [
            [['entity_id', 'entity', 'file', 'sort'], 'required'],
            [['entity_id', 'entity', 'sort'], 'integer'],
            [['file'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['entity_id', 'entity', 'file'], 'unique', 'targetAttribute' => ['file']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_id' => 'Entity ID',
            'entity' => 'Entity',
            'file' => 'File',
            'name' => 'Название',
            'sort' => 'Sort',
        ];
    }
}
