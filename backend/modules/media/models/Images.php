<?php

namespace backend\modules\media\models;

use Yii;

class Images extends \yii\db\ActiveRecord
{
    /**
     * @property int $id
     * @property int $entity_id
     * @property int $entity
     * @property string $img
     *
     * @return string
     */

    public static function tableName()
    {
        return 'media_images';
    }

    public function rules()
    {
        return [
            [['entity_id', 'entity', 'img', 'sort'], 'required'],
            [['entity_id', 'entity', 'sort'], 'integer'],
            [['img'], 'string', 'max' => 255],
            [['entity_id', 'entity', 'img'], 'unique', 'targetAttribute' => ['entity_id', 'entity', 'img']],
        ];
    }
}
