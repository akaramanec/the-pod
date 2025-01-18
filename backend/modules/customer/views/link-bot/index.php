<?php

use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\CustomerTagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ссылки с метриками';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::create('Добавить ссылку');
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        'name',
        [
            'label' => 'Зарегистрированных',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->countCustomer();
            },
        ],
        [
            'label' => 'Купивших продукт',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->countOrder();
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '100'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/customer/link-bot/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'data-category_id' => $key,
                            'title' => 'Изменить',
                        ]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/customer/link-bot/delete', 'id' => $key],
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

