<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */

$this->title = Yii::t('app', 'Edit product mod') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

