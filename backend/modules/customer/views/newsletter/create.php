<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\Newsletter */

$this->title = 'Создать рассылку';
$this->params['breadcrumbs'][] = ['label' => 'Рассылка', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>
