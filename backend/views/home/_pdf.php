<?php
/**
 * @var $this \yii\web\View
 * @var $cart Cart
 * @var $model backend\modules\rent\models\Move
 * @var $form yii\widgets\ActiveForm
 */

use backend\modules\rent\models\cart\Cart;

?>

<?php if ($cart->itemsInCategory): ?>
    <div id="pdf-main">
        <div id="head">
            <div class="contact">
                <p>www.zodiac.film</p>
                <p>Leninska kuznya</p>
                <p>26 Elektrykiv</p>
                <p>Kyiv 04176</p>
                <p>Ukraine</p>
                <p>+380503874520</p>
                <p>go@zodiac.film</p>
                <p>instagram.com/zodiac.film</p>
            </div>
            <div class="logo">
                <img src="<?= Yii::$app->params['dataUrl'] ?>/img/zodiac-logo2.png" width="100%">
            </div>
            <div class="text">
                <p>Vintage look film service</p>
                <p>Professional drone cinematography</p>
            </div>
            <div class="info">
                <p>Project</p>
            </div>
        </div>
        <div id="body">
            <?php foreach ($cart->itemsInCategory as $category): ?>
                <table width="100%" class="table-main">
                    <thead>
                    <tr>
                        <th><?= $category['categoryName'] ?></th>
                        <th width="10%">price</th>
                        <th width="7%">qty</th>
                        <th width="10%">total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($category['product'] as $item): ?>
                        <tr>
                            <td><?= $item['productName'] ?></td>
                            <td><?= Cart::format($item['productPrice']) ?></td>
                            <td><?= $item['qtyItem'] ?></td>
                            <td><?= Cart::format($item['priceItemProductTotal']) ?></td>

                        </tr>
                        <?php if ($item['set']): ?>
                            <?php foreach ($item['set'] as $set): ?>
                                <tr>
                                    <td><?= $set['setName'] ?></td>
                                    <td><?= Cart::format($set['setPrice']) ?></td>
                                    <td><?= $set['setQty'] ?></td>
                                    <td><?= Cart::format($set['priceItemSetTotal']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                    <?php endforeach ?>
                    <tr>
                        <th><?= $category['categoryName'] ?> subtotal:</th>
                        <th></th>
                        <th></th>
                        <th><?= $category['priceItemCategoryTotal'] ?></th>
                    </tr>
                    </tbody>
                </table>
            <?php endforeach ?>
        </div>
        <div id="footer">
            <div class="info">
                <table>
                    <tbody>
                    <tr>
                        <td class="font-weight-bold">Цена всего:</td>
                        <td><?= Cart::format($cart->sumTotal) ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Общее количество:</td>
                        <td><?= $cart->qtyTotal ?> шт.</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Количество дней:</td>
                        <td><?= $cart->data->qtyOfShifts ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <h4>Продуктов нет</h4>
<?php endif; ?>









