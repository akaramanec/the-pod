<?php

namespace backend\modules\shop\models;

use backend\modules\media\models\ImgSave;
use Faker\Provider\Uuid;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

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
 * @property Category $parent
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read Product[] $products
 */
class Category extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => function() { return date('Y-m-d H:i:s'); },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => ImgSave::class,
                'entityImg' => SHOP_CATEGORY
            ]
        ];
    }

    public static function tableName()
    {
        return 'shop_category';
    }

    public function rules()
    {
        return [
            [['parent_id', 'sort', 'status', 'home'], 'integer'],
            [['name', 'status'], 'required'],
            [['uuid', 'slug', 'description'], 'string'],
            [['uuid'], 'string', 'max' => 50],
            [['name', 'slug'], 'string', 'max' => 255],
            [['img'], 'string'],
            [['slug'], 'unique'],
            [['uuid'], 'unique'],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 1],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => Yii::t('app', 'Parent'),
            'uuid' => 'Uuid',
            'name' => Yii::t('app', 'Name'),
            'slug' => 'Slug',
            'description' => 'Description',
            'sort' => Yii::t('app', 'Sort'),
            'status' => Yii::t('app', 'Status'),
            'img' => Yii::t('app', 'Image'),
            'home' => 'Home',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord && empty($this->uuid)) {
                do {
                    $this->uuid = Uuid::uuid();
                } while (Category::find()->where(['uuid' => $this->uuid])->exists());
            }
            return true;
        }
        return false;
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    public static function getByUuid($uuid)
    {
        return self::find()->where(['uuid' => $uuid])->limit(1)->one();
    }

    public static function createTree()
    {
        $categories = self::find()->where(['status' => self::STATUS_ACTIVE])->all();
        $tree = [];
        foreach ($categories as $category) {
            $tree[$category->parent_id][] = $category;
        }
        return $tree;
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function statusName()
    {
        return static::statuses()[$this->status] ?? '';
    }

    public function statusClass()
    {
        $statusClasses = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'danger',
        ];

        return $statusClasses[$this->status];
    }
}
