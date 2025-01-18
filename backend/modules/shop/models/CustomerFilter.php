<?php

namespace backend\modules\shop\models;

use backend\modules\customer\models\Customer;
use Yii;

/**
 * @property int $customer_id
 * @property int $attribute_value_id
 */
class CustomerFilter extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_customer_filter';
    }

    public function rules()
    {
        return [
            [['customer_id', 'attribute_value_id'], 'required'],
            [['customer_id', 'attribute_value_id'], 'integer'],
            [['customer_id', 'attribute_value_id'], 'unique', 'targetAttribute' => ['customer_id', 'attribute_value_id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['attribute_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttributeValue::class, 'targetAttribute' => ['attribute_value_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer ID',
            'attribute_value_id' => 'Attribute Value ID',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getAttributeValue()
    {
        return $this->hasOne(AttributeValue::class, ['id' => 'attribute_value_id']);
    }

    public static function saveItemVb()
    {
        if (isset(Yii::$app->vb->data->attribute_value_id) && isset(Yii::$app->vb->customer->id)) {
            self::saveItem(Yii::$app->vb->customer->id, Yii::$app->vb->data->attribute_value_id);
        }
    }

    public static function saveItemTm()
    {
        if (isset(Yii::$app->tm->data->av_id) && isset(Yii::$app->tm->customer->id)) {
            self::saveItem(Yii::$app->tm->customer->id, Yii::$app->tm->data->av_id);
        }
    }

    public static function saveItem($customer_id, $attribute_value_id)
    {
        $filter = self::find()
            ->where(['customer_id' => $customer_id])
            ->andWhere(['attribute_value_id' => $attribute_value_id])
            ->one();
        if ($filter === null) {
            $filter = new self();
            $filter->customer_id = $customer_id;
            $filter->attribute_value_id = $attribute_value_id;
            $filter->save();
        } else {
            $filter->delete();
        }
    }

    public static function attributeValueId($customer_id)
    {
        $customerFilter = CustomerFilter::find()
            ->where(['customer_id' => $customer_id])
            ->with(['attributeValue'])
            ->asArray()
            ->all();
        if ($customerFilter === null) {
            return [];
        }
        $attribute_value_id = [];
        foreach ($customerFilter as $item) {
            if (isset($attribute_value_id[$item['attributeValue']['attribute_id']])) {
                $attribute_value_id[$item['attributeValue']['attribute_id']] = array_merge($attribute_value_id[$item['attributeValue']['attribute_id']], [$item['attribute_value_id']]);
            } else {
                $attribute_value_id[$item['attributeValue']['attribute_id']][] = $item['attribute_value_id'];
            }
        }
        return $attribute_value_id;
    }
}
