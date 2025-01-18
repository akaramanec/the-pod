<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */

$this->title = 'Команда';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bot Commands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

