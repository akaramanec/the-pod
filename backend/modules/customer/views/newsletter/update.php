<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Newsletter */

$this->title = 'Рассылка';
$this->params['breadcrumbs'][] = ['label' => 'Рассылки', 'url' => ['index']];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>
