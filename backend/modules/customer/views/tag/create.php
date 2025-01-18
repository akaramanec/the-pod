<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\CustomerTag */

$this->title = 'Создать тег';
$this->params['breadcrumbs'][] = ['label' => 'Теги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>

