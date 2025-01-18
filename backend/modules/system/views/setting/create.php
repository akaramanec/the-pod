<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Setting */

$this->title = 'Добавить настройку';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

