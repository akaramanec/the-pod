<?php

use backend\modules\shop\models\Product;
use common\widgets\PaginationWidget;
use backend\modules\media\models\Img;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bot\models\search\BotCommandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Products list');
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';

$this->params['right_content'] .= Buttons::create(Yii::t('app', 'Add product'));
$this->params['pagination'] = PaginationWidget::widget();
?>


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => false,
            'format' => 'raw',
            'contentOptions' => ['class' => 'img-table-circle'],
            'headerOptions' => ['width' => '59', 'class' => 'header-name-table'],
            'value' => function (Product $model) {
                return Img::main(SHOP_CATEGORY, $model->id, $model->img ? $model->img->img : null, '400x400');
            },
        ],
        [
            'headerOptions' => ['width' => '80'],
            'attribute' => 'id',
        ],
        'name',
        'sort',
        [
            'filter' => Product::statuses(),
            'attribute' => 'status',
            'value' => function (Product $model) {
                return Html::tag('span', $model->statusName(), ['class' => 'badge badge-' . $model->statusClass()]);
            },
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update}{delete}</div>',
            'headerOptions' => ['width' => '65'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/shop/product/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => Yii::t('app', 'Edit'),
                        ]);

                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/shop/product/delete', 'id' => $key],
                        [
                            'title' => Yii::t('app', 'Delete'),
                            'class' => 'btn btn-outline-dark',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        ]);
                },
            ],
        ],
    ],
]); ?>



