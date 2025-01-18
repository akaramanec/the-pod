<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\CustomerTag */

$this->title = 'Тег';
$this->params['breadcrumbs'][] = ['label' => 'Теги', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


