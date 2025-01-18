<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotMenu */

$this->title = 'Create Bot Menu';
$this->params['breadcrumbs'][] = ['label' => 'Bot Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

