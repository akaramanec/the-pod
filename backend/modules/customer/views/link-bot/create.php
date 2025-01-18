<?php

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\LinkBot*/

$this->title = 'Добавить ссылку';
$this->params['breadcrumbs'][] = ['label' => 'Ссылки с метриками', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

