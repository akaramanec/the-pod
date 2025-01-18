<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $mod_id
 * @property int $attribute_value_id
 */
class AttributeValueProductModLink extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_attribute_value_product_mod_link';
    }


    public function rules()
    {
        return [
            [['mod_id', 'attribute_value_id'], 'required'],
            [['mod_id', 'attribute_value_id'], 'integer'],
            [['mod_id', 'attribute_value_id'], 'unique', 'targetAttribute' => ['mod_id', 'attribute_value_id']],
            [['mod_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductMod::class, 'targetAttribute' => ['mod_id' => 'id']],
            [['attribute_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeValue::class, 'targetAttribute' => ['attribute_value_id' => 'id']],
        ];
    }

    public function getMod()
    {
        return $this->hasOne(ProductMod::class, ['id' => 'mod_id']);
    }

    public function getAttributeValue()
    {
        return $this->hasOne(AttributeValue::class, ['id' => 'attribute_value_id']);
    }

    public static function attributeByMod($mod_id, $attributeValueId)
    {
        return AttributeValueProductModLink::find()
            ->alias('avpml')
            ->joinWith(['attributeValue AS attributeValue'])
            ->where(['avpml.mod_id' => $mod_id])
            ->andWhere(['avpml.attribute_value_id' => $attributeValueId])
            ->groupBy('avpml.attribute_value_id')
            ->orderBy('attributeValue.sort asc')
            ->asArray()
            ->all();
    }

}
