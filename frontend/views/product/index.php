<?php

use backend\modules\bot\models\BotPlaceholder;
use backend\modules\media\models\Img;
use backend\modules\shop\models\Product;
use src\helpers\Common;

/**
 * @var $this \yii\web\View
 * @var $mod \backend\modules\shop\models\ProductMod
 * @var $meta array
 */
echo \frontend\widgets\Meta::widget([
    'title' => BotPlaceholder::made('meta_title_product', $meta),
    'description' => BotPlaceholder::made('meta_description_product', $meta),
    'img' => Img::productImageWithCheckSupportExtension($mod->product->img, Yii::$app->params['sizeProduct']['mid'])
]);
Yii::$app->site->microMarking = [
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    'name' => $mod->product->name,
    'image' => Img::productImageWithCheckSupportExtension($mod->product->img, Yii::$app->params['sizeProduct']['mid']),
    'description' => $mod->product->description,
    'brand' => [
        '@type' => 'Thing',
        'name' => Product::attributeByName($mod->product->attribute_cache, 'Бренд'),
    ],
    'offers' => [
        '@type' => 'Offer',
        'url' => Common::fullUrl(),
        'priceCurrency' => 'UAH',
        'price' => (string)$mod->product->price
    ],
];
?>
<main class="main product">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <section class="product__main">
        <div class="container">
            <div class="product__content">
                <div class="product__img">
                    <img class="product__img-main"
                         src="<?= Img::productImageWithCheckSupportExtension($mod->product->img, Yii::$app->params['sizeProduct']['big']) ?>"
                         alt="<?= $mod->product->name ?>">
                </div>
                <div class="product__description-main">
                    <p><strong>Код:</strong> <?= $mod->product->code ?></p>
                    <h1><?= $mod->product->name ?></h1>
                    <ul class="product__stats">
                        <?php if ($mod->product->attribute_cache): ?>
                            <?php foreach ($mod->product->attribute_cache as $attribute): ?>
                                <li>
                                    <?= $attribute['name'] ?>:
                                    <span> <?= implode(', ', $attribute['value']) ?></span>
                                </li>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </ul>
                    <?php if ($mod->product->qty_total > 0): ?>
                        <p class="product__price">Цена: <span> <?= $mod->product->price ?></span></p>

                        <div class="info-wrap">
                            <?= $this->render('@frontend/views/common/_qty', [
                                'modId' => $mod->id,
                                'minusCss' => 'minus-static',
                                'plusCss' => 'plus-static'
                            ]) ?>
                        </div>
                        <button class="base-button item-card__btn btn--dark add-to-cart"
                                data-id="<?= $mod->id ?>">В корзину
                        </button>
                        <button data-id="<?= $mod->id ?>"
                                class="base-button item-card__btn btn--dark fast-order">
                            Оформить заказ
                        </button>
                    <?php endif; ?>
                    <?php if ($mod->product->qty_total < 1): ?>
                        <div class="bg-text-danger">
                            <p class="text-danger">Нет в наличии</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product__description-second">
                    <?php if ($mod->product->description): ?>
                        <?= $mod->product->description ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>


