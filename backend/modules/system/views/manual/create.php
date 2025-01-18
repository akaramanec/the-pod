<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Manual */

$this->title = 'Create Manual';
$this->params['breadcrumbs'][] = ['label' => 'Manual', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
