<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\shop\models\Faq */

$this->title = 'Создать FAQ';
$this->params['breadcrumbs'][] = ['label' => 'FAQ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>
