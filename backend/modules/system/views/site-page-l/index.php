<?php

use src\helpers\Buttons;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\SitePageLSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Содержание страниц';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create('Добавить содержание');
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'headerOptions' => ['width' => '100'],
            'attribute' => 'id',
        ],
        [
            'headerOptions' => ['width' => '100'],
            'attribute' => 'page_id',
        ],
        [
            'headerOptions' => ['width' => '80'],
            'attribute' => 'lang',
        ],
        [
            'headerOptions' => ['width' => '300'],
            'attribute' => 'name',
        ],
//        'description:ntext',
//        'meta',
        'content:ntext',
//        'addition',
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '65'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/system/site-page-l/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/system/site-page-l/delete', 'id' => $key],
                        [
                            'title' => 'Удалить',
                            'class' => 'btn btn-outline-dark',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                        ]);
                }
            ],
        ],

        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
