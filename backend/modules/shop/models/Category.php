<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $uuid
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $sort
 * @property int $status
 * @property string|null $img
 * @property int|null $home
 */
class Category extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'shop_category';
    }

    public function rules()
    {
        return [
            [['parent_id', 'sort', 'status', 'home'], 'integer'],
            [['uuid', 'name', 'slug', 'status'], 'required'],
            [['description'], 'string'],
            [['uuid'], 'string', 'max' => 50],
            [['name', 'slug'], 'string', 'max' => 255],
            [['img'], 'string', 'max' => 20],
            [['slug'], 'unique'],
            [['uuid'], 'unique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'uuid' => 'Uuid',
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'sort' => 'Sort',
            'status' => 'Status',
            'img' => 'Img',
            'home' => 'Home',
        ];
    }

    public function getProduct()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    public static function getByUuid($uuid)
    {
        return self::find()->where(['uuid' => $uuid])->limit(1)->one();
    }
}
