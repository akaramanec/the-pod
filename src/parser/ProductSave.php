<?php

namespace src\parser;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\ApiProduct;
use backend\modules\media\models\Images;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductTemp;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @property \backend\modules\bot\src\ApiProduct $api
 */
class ProductSave
{
    public $api;
    private $_slugAddNumber = 1;

    public function __construct($api)
    {
        $this->api = $api;
        $this->setStatus();
        $this->start();
    }

    public function __destruct()
    {
    }


    public function start()
    {
        foreach (ProductTemp::find()->each() as $item) {
            $product = $this->getProduct($item->uuid);
            if ($product->isNewRecord) {
                $product->uuid = $item->uuid;
                $product->slug = $this->generateSlug($item->slug);
                $product->status = Product::STATUS_ACTIVE;
                $product->code = $item->code;
            }
            $product->category_id = $item->category_id;
            $product->description = $item->description;
            $product->name = $item->name;
            $product->price = $item->price;
            $product->qty_total = $item->qty;
            if ($item->qty > 0) {
                $product->status = Product::STATUS_ACTIVE;
            }
            if (!$product->save()) {
                BotLogger::save_input(['ProductSave start', $product->errors]);
            }
            $path = Yii::getAlias('@backend/web/uploads/product/' . $product->id);
            if (!is_dir($path)) {
                $this->api->saveImg($product->id, $item->img['rows']);
            }
            sleep(1);
        }
    }

    public function setStatus()
    {
        foreach (Product::find()->each() as $product) {
            if (ProductTemp::find()->where(['uuid' => $product->uuid])->limit(1)->exists()) {
                continue;
            }
            $product->status = Product::STATUS_INACTIVE;
            $product->save(false);
        }
    }

    private function generateSlug($name)
    {
        $slug = Inflector::slug($name);
        $model = Product::find()->where(['slug' => $slug])->limit(1)->one();
        if ($model) {
            $newSlug = $slug . $this->_slugAddNumber++;
            return $this->generateSlug($newSlug);
        } else {
            $this->_slugAddNumber = 1;
            return $slug;
        }
    }

    public function getProduct($uuid)
    {
        $product = Product::find()->where(['uuid' => $uuid])->limit(1)->one();
        if ($product) {
            return $product;
        }
        return new Product();
    }


}
