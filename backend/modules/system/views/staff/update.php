<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\WidgetSliderHome */

$this->title = 'Обновить сотрудника: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Штат сотрудников', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];

?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

