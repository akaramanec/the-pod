<?php
$this->title = 'Админ';
$this->params['breadcrumbs'][] = ['label' => 'Админы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->surname . ' ' . $model->name];

?>


<?= $this->render('_form', [
    'model' => $model,
]) ?>


