<?php


$this->title = 'Update Manual: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Manual', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
?>
<div class="box">
    <div class="box-body">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
