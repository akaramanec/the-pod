<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $catalogPage \src\services\CatalogPage
 * @var $searchModel \backend\modules\shop\models\search\ProductModSearch
 * @var array $modId
 * @var array $priceMaxMin
 */


use src\helpers\Common;
use yii\bootstrap4\LinkPager;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

echo \frontend\widgets\Meta::widget([
    'title' => $catalogPage->meta['title'],
    'description' => $catalogPage->meta['description'],
    'img' => $catalogPage->img
]);

Yii::$app->site->microMarking = [
    '@context' => 'http://schema.org',
    '@type' => 'Product',
    'name' => $catalogPage->name,
    'offers' => [
        '@type' => 'AggregateOffer',
        'offerCount' => $dataProvider->getTotalCount(),
        'highPrice' => $priceMaxMin['max'],
        'lowPrice' => $priceMaxMin['min'],
        'priceCurrency' => 'UAH'
    ]
];
$this->params['canonical'] = Url::to(['catalog/index'], true);
?>
<main class="main catalog">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div class="main-catalog">
        <div class="container">
            <div class="catalog__content">
                <div class="catalog__filter">
                    <?= frontend\widgets\FilterWidget::widget([
                        'mod_id' => $modId,
                        'searchModel' => $searchModel,
                        'slug' => $catalogPage->slug,
                        'priceMaxMin' => $priceMaxMin,
                    ]); ?>
                </div>
                <div class="catalog__wrap">
                    <?php foreach ($dataProvider->getModels() as $mod): ?>
                        <?= $this->render('@frontend/views/common/_product_item_vertical', [
                            'mod' => $mod,
                        ]) ?>
                    <?php endforeach ?>
                    <div hidden>
                        <button>
                            <a href=""
                               id="search_link">search_link</a></button>
                        <div id="this_url"><?= Yii::$app->params['homeUrl'] . $catalogPage->url ?></div>
                        <div id="category_slug"><?= $catalogPage->slug ?></div>
                    </div>
                </div>
            </div>
            <?= LinkPager::widget(ArrayHelper::merge([
                'pagination' => $dataProvider->getPagination(),
            ], Common::pager4(5))); ?>
        </div>
    </div>
</main>









