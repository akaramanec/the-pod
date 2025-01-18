<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\CustomerMessage */

$this->title = 'Сообщение от пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Сообщения от пользователя', 'url' => ['index']];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

