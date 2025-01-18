<?php

use backend\modules\media\models\Img;
use backend\modules\shop\models\Order;
use frontend\models\cart\Cart;
use src\helpers\Common;

/**
 * @var object $page
 * @var $order Order
 * @var $cart Cart
 * @var $this \yii\web\View
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' => Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);
$isShowTrackingTradeJS = Yii::$app->session->get('ShowTrackingTradeJS');
Yii::$app->session->set('ShowTrackingTradeJS',false);

$trackingTradeArray = [
    'event' => 'purchase',
    'transactionId' => $order->id,
    'transactionTotal' => $cart->sumTotal,
    'transactionProducts' => [],
];
function setTrackingProduct(array $product, &$trackingTradeArray)
{
    $trackingTradeArray['transactionProducts'][] = [
        'sku' => (string)$product['codeProduct'],
        'name' => (string)$product['productName'],
        'category' => (string)$product['productName'],
        'price' => (int)$product['priceItemProductTotal'],
        'quantity' => (int)$product['qtyItem']
    ];
}
$jsTrackingTrade = <<<JS
    window.dataLayer = window.dataLayer || []
    dataLayer.push(JSON.parse($("#trackingTradeData").html()));
JS;

?>
<main class="main">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div class="success-popup">
        <div class="success-popup__content">
            <h2>Спаибо за заказ!</h2>
            <span class="cross"></span>
            <div class="success-popup__product-wrap">
                <?php foreach ($cart->items as $item): ?>
                    <?php setTrackingProduct($item, $trackingTradeArray); ?>
                    <div class="success-popup__item">
                        <img class="success-popup__item-img"
                             src="<?= $item['img'] ?>"
                             alt="<?= $item['productName'] ?>"
                             title="<?= $item['productName'] ?>"
                             width="10">
                        <div class="success-popup__item-description">
                            <p class="success-popup__item-name"
                               content=""><b><?= $item['productName'] ?></b></p>
                            <p class="success-popup__item-qty">Количество <?= $item['qtyItem'] ?> шт.</p>
                            <p class="success-popup__item-priceall"><?= $cart->showPrice($item['priceItemProductTotal']) ?></p>
                        </div>
                    </div>

                <?php endforeach ?>
            </div>

            <footer class="success-popup__footer">
                <div class="success-popup__summ">
                    <p>Цена всего: <?= $cart->showPrice($cart->sumTotal) ?></p>
                    <p>Общее количество: <?= $cart->qtyTotal ?> шт.</p>
                    <p>Оплата: <?= Order::statusesPaymentMethod()[$order->payment_method] ?></p>
                </div>
                <p class="success-popup__msg">Мы уведомим вас о любом изменении в заказе по почте или в мессенджере Telegram</p>
                <div class="main-chatbot__buttons">
                    <a class="base-button btn--dark main-chatbot__btn"
                       href="<?= Yii::$app->site->linkTm() ?>">telegram</a>
                </div>
            </footer>
        </div>
    </div>
</main>

<script type="application/json" id="trackingTradeData">
    <?= json_encode($trackingTradeArray); ?>
</script>

<?php
if ($isShowTrackingTradeJS) {
    $this->registerJs($jsTrackingTrade);
}
?>