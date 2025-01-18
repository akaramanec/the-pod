<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Poll */

$this->title = 'Опрос';
$this->params['breadcrumbs'][] = ['label' => 'Опросы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
