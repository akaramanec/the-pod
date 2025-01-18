<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\CustomerMessage */

$this->title = 'Create Customer Message';
$this->params['breadcrumbs'][] = ['label' => 'Customer Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
