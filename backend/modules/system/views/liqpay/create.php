<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Liqpay */

$this->title = Yii::t('app', 'Create Liqpay');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Liqpays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


