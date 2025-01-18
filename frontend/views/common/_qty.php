<?php

use frontend\models\cart\Cart;

/**
 * @var integer $modId
 * @var string $minusCss
 * @var string $plusCss
 */
?>

<div class="btn-toolbar d-flex align-items-center"
     role="toolbar">
    <div class="btn-group d-flex align-items-center mr-2"
         role="group">
        <button type="button" class="<?= $minusCss ?> minus" data-id="<?= $modId ?>">-</button>
        <input type="text"
               size="3"
               class="input_qty input_qty_<?= $modId ?>"
               value="<?= Cart::qtyItem($modId); ?>"
               data-id="<?= $modId ?>"/>
        <button type="button" class="<?= $plusCss ?> plus" data-id="<?= $modId ?>">+</button>
    </div>
</div>
