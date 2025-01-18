<?php
/**
 * @var $this \yii\web\View
 * @var $mod \backend\modules\shop\models\ProductMod
 */

use backend\modules\media\models\Img;

?>
<div class="item-card">
    <aside class="item-card__img">
        <a class="item-card__img--base" href="/product/<?= $mod->product->slug ?>">
            <div class="img-wrap">
                <img src="<?= Img::productImageWithCheckSupportExtension($mod->product->img, Yii::$app->params['sizeProduct']['mid']) ?>"
                     alt="<?= $mod->product->name ?>">
            </div>
        </a>
        <a class="item-card__img--hover" href="/product/<?= $mod->product->slug ?>">
            <div class="img-wrap">
                <img src="<?= Img::productImageWithCheckSupportExtension($mod->product->img, Yii::$app->params['sizeProduct']['mid']) ?>"
                     alt="<?= $mod->product->name ?>">
            </div>
        </a>
    </aside>
    <div class="item-card__body">
        <a class="link item-card__link" href="/product/<?= $mod->product->slug ?>">
            <span><?= $mod->product->name ?></span>
        </a>
        <?php $price = $mod->product->price;
        \src\services\AdditionalDiscount::updateProductItemPrice($price, $mod->product->name, true);
        $mod->product->price = $price;
        ?>
        <span class="item-card__price"><?= $mod->product->price ?></span>
    </div>
    <footer class="item-card__buttons">
        <a href="/fast-order/<?= $mod->id ?>/1" class="base-button item-card__btn btn--dark">Оформить заказ</a>
        <button class="base-button item-card__btn btn--dark add-to-cart" data-id="<?= $mod->id ?>">В корзину</button>
    </footer>
</div>
