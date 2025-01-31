<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */

$this->title = Yii::t('app', 'Create product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


