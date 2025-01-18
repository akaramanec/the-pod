<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\LinkBot*/

$this->title = 'Ссылка';
$this->params['breadcrumbs'][] = ['label' => 'Ссылки с метриками', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


