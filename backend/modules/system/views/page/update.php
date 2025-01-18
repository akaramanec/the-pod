<?php

$this->title = 'Изменить страницу';
$this->params['breadcrumbs'][] = ['label' => 'Страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $pageL->name];
?>


<?= $this->render('_form', [
    'page' => $page,
    'pageL' => $pageL,
]) ?>

