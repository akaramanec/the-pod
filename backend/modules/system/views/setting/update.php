<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Setting */

$this->title = 'Настройка';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];

?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

