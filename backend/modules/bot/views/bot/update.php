<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\Bot */

$this->title = $model->platform;
$this->params['breadcrumbs'][] = ['label' => 'Bot', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


