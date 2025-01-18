<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\WidgetSliderHome */

$this->title = 'Создать сотрудника';
$this->params['breadcrumbs'][] = ['label' => 'Штат сотрудников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

