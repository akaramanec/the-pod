<?php

use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Manuals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">

        <p>
            <?= Html::a('Create Manual', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'id',
                    'headerOptions' => ['width' => '80'],
                ],
                [
                    'label' => 'Name',
                    'attribute' => 'name',
                    'value' => function ($data) {
                        return Html::a($data->name, ['update', 'id' => $data->id]);
                    },
                    'format' => 'raw',
                ],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
