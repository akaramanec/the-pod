<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Attribute */

$this->title = 'Update Attribute value';
$this->params['breadcrumbs'][] = ['label' => 'Attribute', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-value-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-value', [
        'model' => $model,
    ]) ?>

</div>