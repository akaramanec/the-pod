<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\ProductMod */

$this->title = Yii::t('app', 'Create product mod');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


