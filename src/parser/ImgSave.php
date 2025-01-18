<?php

namespace src\parser;

use backend\modules\media\models\Images;
use backend\modules\media\models\Img;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductTemp;
use Yii;
use yii\helpers\BaseFileHelper;

/**
 * @property \backend\modules\bot\src\ApiProduct $api
 */
class ImgSave
{
    public $api;

    public function __construct($api, $new_cache = false)
    {
        $this->api = $api;
        $this->start();
        $this->checkImg();
        if ($new_cache) {
            $this->newCache();
        }
    }

    public function start()
    {
        foreach (ProductTemp::find()->each() as $item) {
            $product = Product::find()->where(['uuid' => $item->uuid])->limit(1)->one();
            if ($product) {
                if ($item->img['rows']) {
                    $this->api->saveImg($product->id, $item->img['rows']);
                }
                sleep(1);
            }
        }
    }

    public function newCache()
    {
        $path = Yii::$app->params['imgPath'] . '/cache/product';
        BaseFileHelper::removeDirectory($path);
        BaseFileHelper::createDirectory($path, 0777);
        foreach (Product::find()->each() as $product) {
            if ($product->images) {
                foreach ($product->images as $img) {
                    Img::i($img, Yii::$app->params['sizeProduct']['mid_square']);
                    Img::i($img, Yii::$app->params['sizeProduct']['mid']);
                    Img::i($img, Yii::$app->params['sizeProduct']['big']);
                }
            }
        }
    }

    public function checkImg()
    {
        foreach (Product::find()->each() as $product) {
            if ($product->images && count($product->images) <= 1) {
                Images::updateAll(['sort' => 1], ['id' => $product->images[0]->id]);
            }
        }
    }
}
