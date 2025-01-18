<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotMenuCommand */

$this->title = 'Update Bot Menu Command: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bot Menu Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

