<?php

namespace frontend\controllers;

use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\search\ProductModSearch;
use backend\modules\system\models\SitePage;
use src\helpers\CategoryHelp;
use src\helpers\DieAndDumpHelper;
use src\services\CatalogPage;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CatalogController extends Controller
{
    private $_category;
    private $_categoryIdTreeChildren;
    private $_categoryTreeChildren;
    private $_searchModel;
    private $_dataProvider;
    private $_priceMaxMin;

    public function actionIndex($slug = null)
    {
        if ($slug) {
            $this->category($slug);
            $catalogPage = new CatalogPage($this->_category, 'slug');
        } else {
            $catalogPage = new CatalogPage(SitePage::page('catalog-all'), 'catalog-all');
        }
        $this->searchModel();
        return $this->render('index', [
            'catalogPage' => $catalogPage,
            'categoryIdTreeChildren' => $this->_categoryIdTreeChildren,
            'categoryTreeChildren' => $this->_categoryTreeChildren,
            'searchModel' => $this->_searchModel,
            'dataProvider' => $this->_dataProvider,
            'modId' => $this->modId(),
            'priceMaxMin' => $this->_priceMaxMin,
        ]);
    }

    private function searchModel()
    {
        $this->_searchModel = new ProductModSearch();
        $this->_dataProvider = $this->_searchModel->search($this->_searchModel->parsingAddressBar());
        $this->_searchModel->query->andFilterWhere([
            'product.status' => Product::STATUS_ACTIVE,
            'mod.status' => ProductMod::STATUS_ACTIVE,
            'category.status' => Category::STATUS_ACTIVE
        ])
            ->andWhere(['>=', 'product.qty_total', 1])
            ->joinWith([
                'attributeBrand AS attributeBrand'
            ])->orderBy(['attributeBrand.sort' => SORT_ASC])->groupBy('mod.product_id')->with(['product.img']);
        $this->_dataProvider->setPagination(['pageSize' => 24]);
    }

    private function modId()
    {
        $searchModel = new ProductModSearch();
        $dataProvider = $searchModel->search($searchModel->parsingAddressBar());
        $searchModel->query->select('mod.id');
        $searchModel->query->andFilterWhere([
            'product.status' => Product::STATUS_ACTIVE,
            'mod.status' => ProductMod::STATUS_ACTIVE,
            'category.status' => Category::STATUS_ACTIVE,
        ]);
        $searchModel->query->andWhere(['>=', 'product.qty_total', 1]);
        $dataProvider->setPagination(['pageSize' => false]);
        $key = $dataProvider->getKeys();
        $this->_priceMaxMin = ProductMod::priceMaxMin($key);
        return $key;
    }

    public function actionSearch($q)
    {
        $q = trim($q);
        Yii::$app->common->q = $q;
        $searchModel = new ProductModSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->joinWith(['attributeValue AS attributeValue']);
        $searchModel->query->with(['images']);
        $searchModel->query->andWhere([
            'mod.status' => ProductMod::STATUS_ACTIVE,
        ]);

        $searchModel->query->andFilterWhere(['or',
            ['like', 'product.name', $q],
            ['like', 'mod.code', $q],
            ['like', 'product.slug', $q],
        ]);
        $searchModel->query->groupBy('mod.product_id');

        $dataProvider->setPagination(['pageSize' => 21]);
        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'page' => SitePage::page('search')
        ]);
    }

    private function category($slug)
    {
        $this->_category = Category::find()
            ->where(['slug' => $slug])
            ->andWhere(['status' => Category::STATUS_ACTIVE])
            ->limit(1)
            ->one();
        if ($this->_category !== null) {
            $this->_categoryTreeChildren = CategoryHelp::getChildren($this->_category);
            $this->_categoryIdTreeChildren = ArrayHelper::getColumn($this->_categoryTreeChildren, 'id');
            return $this->_category;
        }
        throw new NotFoundHttpException('Категория не содержит товаров.');
    }

}
