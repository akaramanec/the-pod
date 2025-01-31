<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\AttributeValue */

$this->title = 'Create Attribute Value';
$this->params['breadcrumbs'][] = ['label' => 'Attributes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attribute-value-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-value', [
        'model' => $model,
    ]) ?>

</div>
