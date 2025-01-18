<?php
/**
 * @var $noticeNp \backend\modules\shop\models\NoticeNp
 * @var $customer \backend\modules\customer\models\Customer
 */
?>
<h3>Заказ №: <?= $order->id ?></h3>
<h3>Номер заказа новой почты №: <?= $order->np->documentData['IntDocNumber'] ?></h3>
<pre><?= $noticeNp->text ?></pre>
