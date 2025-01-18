<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotCommand */

$this->title = Yii::t('app', 'Create Bot Command');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bot Commands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


