<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Faq */

$this->title = 'FAQ';
$this->params['breadcrumbs'][] = ['label' => 'FAQ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];

?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

