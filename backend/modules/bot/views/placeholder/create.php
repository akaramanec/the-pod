<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotPlaceholder */

$this->title = 'Добавить форму';
$this->params['breadcrumbs'][] = ['label' => 'Bot Placeholders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

