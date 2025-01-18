<?php

use backend\modules\customer\models\Customer;
use blog\models\CustomerBlog;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\Customer */
/* @var $customerBlog CustomerBlog */

$this->title = Yii::t('app', 'Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Customer::fullName($model)];

?>
<?= $this->render('_form', [
    'model' => $model,
    'customerBlog' => $customerBlog,
]) ?>


