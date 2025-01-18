<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\notification\models\form\NotificationForm */

$this->title = 'Редактировать уведомление: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Уведомления', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

