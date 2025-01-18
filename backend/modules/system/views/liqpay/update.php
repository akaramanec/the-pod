<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Liqpay */

$this->title = Yii::t('app', 'Update Liqpay: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Liqpays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id];

?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>
