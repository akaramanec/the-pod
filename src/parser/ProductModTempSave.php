<?php

namespace src\parser;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\ApiProduct;
use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductTemp;
use backend\modules\shop\models\ProductModTemp;
use src\validators\Is;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @var $api ApiProduct
 */
class ProductModTempSave
{
    public $api;
    private $_slugAddNumber = 1;
    private $_offset = 100;

    public function __construct(ApiProduct $api)
    {
        $this->api = $api;
        Yii::$app->db->createCommand("TRUNCATE TABLE shop_product_mod_temp")->execute();
        $this->start($this->api->attributes(['limit' => $this->_offset])['rows']);

    }

    public function __destruct()
    {
    }

    public function start($items, $x = 0)
    {
        if ($items) {
            foreach ($items as $item) {
                $p = new ProductModTemp();
                $p->product_id = $this->getProductId($item);
                $p->uuid = $item['id'];
                $p->code = $item['code'];
                $p->name = $item['name'];
                $p->characteristics = $item['characteristics'];
                $p->slug = $this->generateSlug($item['name']);
                if (isset($item['description'])) {
                    $p->description = $item['description'];
                }
                if (isset($item['salePrices'][0]['value'])) {
                    $p->price = $item['salePrices'][0]['value'];
                }

                if (!$p->save()) {
                    BotLogger::save_input(['ProductModTempSave start', $p->errors]);
                }
            }
            $x += $this->_offset;

            $this->start($this->api->attributes(['limit' => $this->_offset, 'offset' => $x])['rows'], $x);
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

    public function getProductId($item)
    {
        $href = explode('/', $item['product']['meta']['href']);
        $product = Product::find()->where(['uuid' => end($href)])->limit(1)->one();
        if ($product) {
            return $product->id;
        }
    }


}
