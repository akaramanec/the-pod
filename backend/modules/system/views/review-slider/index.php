<?php

use backend\modules\media\models\Img;
use backend\modules\system\models\ReviewSlider;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\WidgetSliderHomeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Слайд отзывов';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create('Добавить слайд отзывов');
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['width' => '80'],
        ],
        [
            'label' => 'Img',
            'format' => 'raw',
            'headerOptions' => ['width' => '100'],
            'value' => function ($data) {
                return Img::main(REVIEW_SLIDER, $data->id, $data->img, '400x400');
            },
        ],
        'name',
        'description',
        [
            'headerOptions' => ['width' => '50'],
            'filter' => ReviewSlider::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return ReviewSlider::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'headerOptions' => ['width' => '80'],
            'attribute' => 'sort',
            'value' => function ($data) {
                return $data->sort;
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/system/review-slider/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'data-category_id' => $key,
                            'title' => 'Изменить',
                        ]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/system/review-slider/delete', 'id' => $key],
                        [
                            'title' => 'Удалить',
                            'class' => 'btn btn-outline-dark',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                        ]);
                },
            ],
        ],
    ],
]); ?>

