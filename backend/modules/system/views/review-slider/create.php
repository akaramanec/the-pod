<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\WidgetSliderHome */

$this->title = 'Добавить слайд отзывов';
$this->params['breadcrumbs'][] = ['label' => 'Слайд отзывов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

