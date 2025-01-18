<?php
/* @var $this yii\web\View */
/* @var $model backend\modules\go\models\Notice */

$this->title = 'Обновить уведомление';
$this->params['breadcrumbs'][] = ['label' => 'Уведомления', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>
