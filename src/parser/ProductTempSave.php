<?php

namespace src\parser;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\ApiProduct;
use backend\modules\shop\models\Category;
use backend\modules\shop\models\ProductTemp;
use src\validators\Is;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @property \backend\modules\bot\src\ApiProduct $api
 */
class ProductTempSave
{
    public $api;
    private $_slugAddNumber = 1;
    private $_offset = 100;
    private $_category;

    public function __construct($api)
    {
        $this->api = $api;
        $this->setCategory();
        Yii::$app->db->createCommand("TRUNCATE TABLE shop_product_temp")->execute();
        $this->start($this->api->productsAll(['limit' => $this->_offset])['rows']);
        $this->img();
    }

    public function __destruct()
    {
    }

    public function start($items, $x = 0)
    {
        if ($items) {
            foreach ($items as $item) {
                // получать последний элемент во вложенности
                $category_name = explode('/', $item['pathName']);
                $category_name = $category_name[count($category_name) - 1];
                $p = new ProductTemp();
                $p->category_id = $this->_category[$category_name];
                $p->uuid = $item['id'];
                $p->code = $item['code'];
                $p->name = $item['name'];
                $p->qty = $this->qty($item['id']);
                $p->slug = $this->generateSlug($item['name']);
                if (isset($item['description'])) {
                    $p->description = $item['description'];
                }
                if (isset($item['salePrices'][0]['value'])) {
                    $p->price = $item['salePrices'][0]['value'] / 100;
                }
                if (!$p->save()) {
                    BotLogger::save_input(['ProductTempSave start', $p->errors]);
                }
            }
            $x += $this->_offset;
            sleep(1);
            $this->start($this->api->productsAll(['limit' => $this->_offset, 'offset' => $x])['rows'], $x);
        }
    }

    public function qty($uuid)
    {
        $assortment = $this->api->assortment('id=' . $uuid);
        if (isset($assortment['rows'][0]['quantity'])) {
            return $assortment['rows'][0]['quantity'];
        }
        return 0;
    }

    public function img()
    {
        $x = 1;
        foreach (ProductTemp::find()->each() as $item) {
            $item->img = $this->api->productImg($item->uuid);
            if (!$item->save()) {
                BotLogger::save_input(['ProductTempSave img', $item->errors]);
            }

            $x++;
            if ($x == 99) {
                sleep(5);
                $x = 1;
            }
        }
    }


    private function generateSlug($name)
    {
        $slug = Inflector::slug($name);
        $model = ProductTemp::find()->where(['slug' => $slug])->limit(1)->one();
        if ($model) {
            $newSlug = $slug . $this->_slugAddNumber++;
            return $this->generateSlug($newSlug);
        } else {
            $this->_slugAddNumber = 1;
            return $slug;
        }
    }

    public function setCategory()
    {
        $this->_category = ArrayHelper::map(Category::find()->all(), 'name', 'id');
    }

}
