<?php

namespace src\parser;

use backend\modules\bot\models\BotLogger;
use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\TmpCategory;
use Yii;
use yii\helpers\Inflector;

/**
 * @property \backend\modules\bot\src\ApiProduct $api
 */
class CategorySave
{
    public $api;

    public function __construct($api)
    {
        $this->api = $api;
        $this->saveTemp($this->api->categories()['rows']);
        $this->setStatus();
        $this->saveCategory();
    }

    public function __destruct()
    {
    }

    public function saveCategory()
    {
        foreach (TmpCategory::find()->each() as $item) {
            $category = $this->getCategory($item->uuid);
            if ($category->isNewRecord) {
                $category->status = Category::STATUS_ACTIVE;
                $category->uuid = $item->uuid;
                $category->slug = $this->slug($item->name);
            }
            $category->name = $item->name;
            $category->save();
            if (!$category->save()) {
                BotLogger::save_input(['saveCategory', $category->errors]);
            }
        }
    }

    private function slug($name, $parent_name = null)
    {
        if ($parent_name) {
            return Inflector::slug($parent_name . '-' . $name);
        }
        return Inflector::slug($name);
    }

    public function parent_id($uuid)
    {
        if ($uuid && $id = Category::getByUuid($uuid)) {
            return $id;
        } else {
            return null;
        }
    }

    public function getCategory($uuid)
    {
        if ($category = Category::getByUuid($uuid)) {
            return $category;
        }
        return new Category();
    }


    public function setStatus()
    {
        foreach (Category::find()->each() as $category) {
            if (TmpCategory::find()->where(['uuid' => $category->uuid])->limit(1)->exists()) {
                $category->status = Category::STATUS_ACTIVE;
                $category->save();
                $this->setStatusProduct($category->id, Product::STATUS_ACTIVE);
                continue;
            }
            $category->status = Category::STATUS_INACTIVE;
            $category->save();
            $this->setStatusProduct($category->id, Product::STATUS_INACTIVE);
        }
    }

    public function setStatusProduct($category_id, $status)
    {
        foreach (Product::find()->where(['category_id' => $category_id])->each() as $product) {
            $product->status = $status;
            $product->save();
            ProductMod::updateAll(['status' => $status], ['product_id' => $product->id]);
        }
    }

    public function saveTemp($items)
    {
        Yii::$app->db->createCommand("TRUNCATE TABLE tmp_category")->execute();
        foreach ($items as $item) {
            $tmpCategory = new TmpCategory();
            $tmpCategory->name = $item['name'];
            $tmpCategory->uuid = $item['id'];
            $tmpCategory->data = $item;
            $tmpCategory->save();
            if (!$tmpCategory->save()) {
                BotLogger::save_input(['saveTemp', $tmpCategory->errors]);
            }
        }
    }
}
