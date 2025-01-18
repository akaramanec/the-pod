<?php

use backend\modules\media\models\Img;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

$this->title = 'Страницы';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';

if (Yii::$app->user->can('dev')) {
    $this->params['right_content'] .= Buttons::create();
}

?>
<div class="bg-element">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => Common::pager4(),
        'tableOptions' => ['class' => 'table'],
        'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
        'columns' => [
            [
                'label' => 'Img',
                'format' => 'raw',
                'headerOptions' => ['width' => '100'],
                'value' => function ($data) {
                    return Img::main(SITE_PAGE, $data->id, $data->img, '400x400');
                },
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->langRu->name, ['/system/page/update', 'id' => $data->id]);
                },
            ],
            'slug',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '50'],
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="far fa-edit"></i>', ['/system/page/update', 'id' => $key],
                            [
                                'class' => 'btn btn-outline-dark',
                                'title' => 'Изменить',
                            ]);

                    },
                ],
            ],
        ],
    ]); ?>
</div>
