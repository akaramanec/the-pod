<?php
/**
 * @var $cart \frontend\models\cart\Cart
 */

use backend\modules\shop\models\Storage;
use frontend\modules\cart\models\Cart;
?>
<?php if ($cart->items): ?>
    <?php foreach ($cart->items as $item): ?>
        <div class="cart__basket">
            <div class="cart__basket-imgwrap">
                <img class="cart__basket-img"
                     src="<?= $item['img'] ?>"
                     alt="<?= $item['productName'] ?>"
                     title="<?= $item['productName'] ?>"
                     width="50">
            </div>
            <div class="cart__basket-content">
                <div class="cart__basket-name">
                    <a href="<?= $item['url'] ?>">
                        <?= $item['productName'] ?>
                    </a>
                    <div class="cabinet-body__favorite-delete del-item"
                         data-id="<?= $item['modId'] ?>">
                        <span class="cabinet-body__favorite-text">Удалить</span>
                        <span class="cabinet-body__favorite-cross"></span>
                    </div>
                </div>

                <div class="cart__basket-info">
                    <div class="info-wrap">
                        <p style="color: lightgrey">Код товара: <?= $item['modId'] ?></p>
                        <?= $this->render('@frontend/views/common/_qty', [
                            'modId' => $item['modId'],
                            'minusCss' => 'minus-modal',
                            'plusCss' => 'plus-modal'
                        ]) ?>
                    </div>
                    <div class="price-wrap">
                        <span class="card-prise price"><?= $cart->showPrice($item['productPrice']) ?></span>
                        <span class="price_total price_item_total_modal_<?= $item['modId'] ?>"><?= $cart->showPrice($item['priceItemProductTotal']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <div hidden>
        <div class="qtyTotal"><?= $cart->qtyTotal ?></div>
        <div class="sumTotal"><?= $cart->showPrice($cart->sumTotal) ?></div>
    </div>
<?php else: ?>
    <p>Корзина пуста</p>
<?php endif; ?>
