<?php

namespace backend\modules\shop\models;

use src\behavior\CapitalLetters;
use Yii;

/**
 *
 * @property-read mixed $product
 * @property-read mixed $attributeValue
 * @property-read mixed $homeBrand
 * @property int $id [int(10) unsigned]
 * @property string $name [varchar(255)]
 * @property bool $type [tinyint(3) unsigned]
 * @property string $uuid [varchar(255)]
 * @property string $slug [varchar(255)]
 * @property int $sort [int(10) unsigned]
 * @property bool $status [tinyint(4)]
 */
class Attribute extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    const ID_BRAND = 22;

    const TYPE_NULL = null;
    const TYPE_STRING = 1;
    const TYPE_COLOR = 2;
    const TYPE_BRAND = 3;
    const TYPE_CUSTOM_TEXT = 4;
    const TYPE_TREE = 8;

    public static function tableName()
    {
        return 'shop_attribute';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CapitalLetters::class,
                'fields' => ['name'],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status', 'type', 'sort'], 'integer'],
            [['name', 'uuid'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'type' => 'Тип',
        ];
    }

    public function getProduct()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])->viaTable('shop_attribute_product_link', ['attribute_value_id' => 'id']);
    }

    public function getAttributeValue()
    {
        return $this->hasMany(AttributeValue::class, ['attribute_id' => 'id'])->orderBy('sort asc');
    }

    public function getHomeBrand()
    {
        return $this->hasMany(AttributeValue::class, ['attribute_id' => 'id'])->with(['img'])->limit(8);
    }

    public static function byType($type)
    {
        return self::find()->where(['type' => $type])->all();
    }

    public static function byName($name)
    {
        return self::find()->where(['name' => $name])->limit(1)->one();
    }

    public static function byUuid($uuid)
    {
        return self::find()->where(['uuid' => $uuid])->limit(1)->one();
    }

    public static function bySlug($slug)
    {
        return self::find()->where(['slug' => $slug])->limit(1)->one();
    }

    public static function listTypeSave()
    {
        return [
            self::TYPE_NULL => 'Нет',
            self::TYPE_STRING => 'Текст',
            self::TYPE_COLOR => 'Цвет',
            self::TYPE_CUSTOM_TEXT => 'Произвольный текст',
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => 'Активный',
            self::STATUS_INACTIVE => 'Выключенный',
        ];
    }

    public static function listTypeView()
    {
        return [
            self::TYPE_NULL => 'Нет',
            self::TYPE_STRING => 'Текст',
            self::TYPE_COLOR => 'Цвет',
            self::TYPE_BRAND => 'Нет',
            self::TYPE_CUSTOM_TEXT => 'П-ный текст',
            self::TYPE_TREE => 'Нет',
        ];
    }
}
