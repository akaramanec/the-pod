<?php

use backend\modules\media\models\Img;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\NewsletterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Рассылка';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= \src\helpers\Buttons::create('Добавить рассылку');
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
            'format' => 'raw'
        ],
        [
            'label' => false,
            'format' => 'raw',
            'contentOptions' => ['class' => 'img-table'],
            'headerOptions' => ['width' => '59', 'class' => 'header-name-table'],
            'value' => function ($data) {
                return Img::main(NEWSLETTER, $data->id, $data->img, '400x400');
            },
        ],
        [
            'attribute' => 'text',
            'format' => 'raw',
            'value' => function ($data) {
                return Common::str($data->text, 0, 220);
            },
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'headerOptions' => ['width' => '150'],
        ],
        [
            'attribute' => 'date_departure',
            'format' => 'datetime',
            'headerOptions' => ['width' => '150'],
        ],
        [
            'filter' => $searchModel->statusesAll(),
            'attribute' => 'status',
            'headerOptions' => ['width' => '150'],
            'format' => 'raw',
            'value' => function ($data) {
                return $data->status();
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{delete}</div>',
            'headerOptions' => ['width' => '100'],
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/customer/newsletter/delete', 'id' => $key],
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

