<?php
/**
 * @var $cart Cart
 */

use frontend\models\cart\Cart;

?>


<table style="width: 100%; border: 1px solid #ddd; border-collapse: collapse;">
    <thead>
    <tr style="background: #f9f9f9;">
        <th style="padding: 8px; border: 1px solid #ddd;">Имя</th>
        <th style="padding: 8px; border: 1px solid #ddd;">Количество</th>
        <th style="padding: 8px; border: 1px solid #ddd;">Цена</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($cart->items as $item): ?>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><?= $item['productName'] ?></td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?= $item['qtyItem'] ?></td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?= $item['priceItemProductTotal'] ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <td colspan="2" style="padding: 8px; border: 1px solid #ddd;">Общее количество:</td>
        <td style="padding: 8px; border: 1px solid #ddd;"><?= $cart->qtyTotal ?> шт.</td>
    </tr>
    <?php if ($cart->sumTotalDiscount): ?>
        <tr>
            <td colspan="2" style="padding: 8px; border: 1px solid #ddd;">Общая сумма:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><s><?= $cart->showPrice($cart->sumTotal) ?></s></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 8px; border: 1px solid #ddd;">Цена всего со скидкой:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?= $cart->showPrice($cart->sumTotalDiscount) ?></td>
        </tr>
    <?php else: ?>
        <tr>
            <td colspan="2" style="padding: 8px; border: 1px solid #ddd;">Общая сумма:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?= $cart->showPrice($cart->cacheSumTotal) ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

