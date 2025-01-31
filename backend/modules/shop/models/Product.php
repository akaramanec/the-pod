<?php

namespace backend\modules\shop\models;

use backend\modules\media\models\Images;
use backend\modules\media\models\ImgSave;
use Faker\Provider\Uuid;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property int $category_id
 * @property int $code
 * @property string|null $name
 * @property string|null $description
 * @property string $slug
 * @property string $uuid
 * @property int $status
 * @property int $qty_total
 *
 * @property-read Category $category
 * @property-read ProductMod[] $mod
 * @property-read Images $img
 * @property-read Images $imgTwo
 * @property-read Images[] $images
 */
class Product extends \yii\db\ActiveRecord
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
                'entityImg' => SHOP_PRODUCT
            ]
        ];
    }

    public static function tableName()
    {
        return '{{%shop_product}}';
    }

    public function rules()
    {
        return [
            [['category_id', 'code'], 'required'],
            [['category_id', 'status', 'qty_total'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['attribute_cache'], 'safe'],
            [['attribute_cache'], 'default', 'value' => []],
            [['name', 'slug'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 10],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
            [['code'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['multiImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 5],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'slug' => 'Slug',
            'status' => 'Status',
            'qty_total' => 'Qty Total',
            'multiImg' => 'Qty Total',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord || $this->isAttributeChanged('name')) {
                $index = 0;
                $this->slug = Inflector::slug($this->name);
                do {
                    $slug = $index++;
                } while (Product::find()->where(['slug' => $slug])->exists());
                $this->slug = $slug;
            }

            if ($this->isNewRecord && empty($this->uuid)) {
                do {
                    $this->uuid = Uuid::uuid();
                } while (Product::find()->where(['uuid' => $this->uuid])->exists());
            }
            return true;
        }
        return false;
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getMod()
    {
        return $this->hasMany(ProductMod::class, ['product_id' => 'id']);
    }

    public function getImg()
    {
        return $this->hasOne(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_PRODUCT])->andWhere(['sort' => 1]);
    }

    public function getImgTwo()
    {
        return $this->hasOne(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_PRODUCT])->andWhere(['sort' => 2]);
    }

    public function getImages()
    {
        return $this->hasMany(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_PRODUCT]);
    }


    public function saveAttributeCache($product)
    {
        $attributeMod = [];
        $attributeNameUnique = [];
        foreach ($product->mod as $mod) {
            $a = [];
            foreach ($mod->attributeValue as $attributeValue) {
                $attributeNameUnique[$attributeValue->shopAttribute->sort] = $attributeValue->shopAttribute->name;
                $a[$attributeValue->shopAttribute->name] = $attributeValue->name;
            }
            $attributeMod[] = $a;
        }

        $attributeCache = [];
        foreach ($attributeNameUnique as $sort => $attributeName) {
            $array_column = array_column($attributeMod, $attributeName);
            $attributeCache[$sort] = [
                'name' => $attributeName,
                'value' => array_unique($array_column)
            ];
        }
        $product->attribute_cache = $attributeCache;
        $product->save(false);
    }

    public static function attributeByName($attribute_cache, $name)
    {
        foreach ($attribute_cache as $item) {
            if ($item['name'] == $name) {
                return $item['value'];
            }
        }
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
