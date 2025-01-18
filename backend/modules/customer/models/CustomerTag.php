<?php

namespace backend\modules\customer\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $name
 */
class CustomerTag extends \yii\db\ActiveRecord
{
    const TAG_NEW = 10;

    public static function tableName()
    {
        return 'bot_customer_tag';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }

    public function getCustomerTagLink()
    {
        return $this->hasMany(CustomerTagLink::className(), ['tag_id' => 'id']);
    }

    public static function listIdName()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    public static function listWithQtyCustomer()
    {

        foreach (self::find()->asArray()->all() as $item) {
            $q = Yii::$app->db->createCommand("SELECT COUNT(*) FROM bot_customer_tag_link WHERE tag_id=:tag_id", [
                ':tag_id' => $item['id']
            ])->queryScalar();
            $list[$item['id']] = $item['name'] . '(' . $q . ')';
        }
        return $list;
    }

    /**
     * @param string $tagName
     * @return static
     */
    public static function getTagModel(string $tagName): self
    {
        $model = self::findOne(['name' => $tagName]);
        if(!isset($model)) {
            $model = new self;
            $model->name = $tagName;
            $model->save();
        }
        return $model;
    }
}
