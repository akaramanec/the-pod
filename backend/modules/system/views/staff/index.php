<?php

use backend\modules\media\models\Img;
use backend\modules\system\models\Staff;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\WidgetSliderHomeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Штат сотрудников';
$this->params['breadcrumbs'][] = $this->title;
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
            'label' => 'Img',
            'format' => 'raw',
            'headerOptions' => ['width' => '50'],
            'value' => function ($data) {
                return Img::main(STAFF, $data->id, $data->img, '400x400');
            },
        ],
        'name',
        'description',
        [
            'headerOptions' => ['width' => '50'],
            'filter' => Staff::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return Staff::status($data->status);
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
                    return Html::a('<i class="far fa-edit"></i>', ['/system/staff/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'data-category_id' => $key,
                            'title' => 'Изменить',
                        ]);
                },
            ],
        ],
    ],
]); ?>
