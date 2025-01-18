<?php

namespace backend\modules\system\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $Ref
 * @property string $AreasCenter
 * @property string $DescriptionRu
 * @property string $Description
 */
class NpArea extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'np_area';
    }


    public function rules()
    {
        return [
            [['Ref', 'AreasCenter', 'DescriptionRu', 'Description'], 'required'],
            [['Ref', 'AreasCenter', 'DescriptionRu', 'Description'], 'string', 'max' => 255],
            [['Ref'], 'unique'],
            [['AreasCenter'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Ref' => 'Ref',
            'AreasCenter' => 'Areas Center',
            'DescriptionRu' => 'Description Ru',
            'Description' => 'Description',
        ];
    }

    public static function listIdName()
    {
        foreach (self::find()->all() as $item) {
            $a[$item->Ref] = $item->DescriptionRu . ' ' . $item->AreasCenter;
        }
        return $a;
    }

}
