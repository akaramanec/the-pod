<?php
/**
 * @var $this \yii\web\View
 * @var $model backend\modules\shop\models\Order
 * @var $cart \frontend\models\cart\Cart
 */

$this->title = 'Заказ: №: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id];
?>
<?= $this->render('_form', [
    'model' => $model,
    'cart' => $cart
]) ?>
