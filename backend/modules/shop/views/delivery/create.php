<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Delivery */

$this->title = 'Create Delivery';
$this->params['breadcrumbs'][] = ['label' => 'Доставка', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


