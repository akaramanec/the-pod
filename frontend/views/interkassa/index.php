<?php

use backend\modules\shop\models\Order;

/**
 * @var $this \yii\web\View
 * @var string $string
 * @var $order \backend\modules\shop\models\Order
 * @var $cart \frontend\models\cart\Cart
 * @var $setting \backend\modules\system\models\Setting
 * @var $platformData \src\services\data\PlatformData
 */
?>
<form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8" hidden>
    <input type="hidden" name="ik_co_id" value="<?= $setting['ik_co_id'] ?>"/>
    <input type="hidden" name="ik_pm_no" value="<?= Order::timeId($order) ?>"/>
    <input type="hidden" name="ik_am" value="<?= $cart->sumTotal ?>"/>
    <input type="hidden" name="ik_cur" value="UAH"/>
    <input type="hidden" name="ik_desc" value="Заказ №: <?= $order->id ?>"/>
    <input type="hidden" name="ik_suc_u" value="<?= $platformData->callbackUrl ?>"/>
    <input id="submit" type="submit" value="Pay">
</form>
<script>
    document.getElementById("submit").click();
</script>
