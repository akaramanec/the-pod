<?php

namespace frontend\controllers;

use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductMod;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProductController extends Controller
{
    private $_mod;

    public function actionIndex($slug)
    {
        $this->_mod = ProductMod::bySlug($slug);
        if ($this->_mod === null) {
            throw new NotFoundHttpException('Такого продукта не существует.');
        }
        $puff = Product::attributeByName($this->_mod->product->attribute_cache, 'Количество Тяг');
        return $this->render('index', [
            'mod' => $this->_mod,
            'meta' => [
                'name' => $this->_mod->product->name,
                'price' => $this->_mod->product->price,
                'puff' => isset($puff[0]) ? $puff[0] : ''
            ],
        ]);
    }



}
