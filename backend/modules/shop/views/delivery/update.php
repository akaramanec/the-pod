<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Delivery */

$this->title = 'Update Delivery: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Доставка', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


