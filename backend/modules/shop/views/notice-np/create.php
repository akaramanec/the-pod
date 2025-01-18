<?php

/* @var $this yii\web\View */


$this->title = 'Создать уведомление НП';
$this->params['breadcrumbs'][] = ['label' => 'Уведомления НП', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

