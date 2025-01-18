<?php


$this->title = 'Добавить админа';
$this->params['breadcrumbs'][] = ['label' => 'Админы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/admin/admin/create']];
?>



<?= $this->render('_form', [
    'model' => $model,
]) ?>


