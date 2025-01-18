<?php

use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\Customer */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= \common\widgets\PaginationWidget::widget();
?>

<div id="user-list" class="bg">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
//        'tableOptions' => ['class' => 'table'],
//        'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive'],
        'pager' => Common::pager4(),
        'columns' => [
            [
                'headerOptions' => ['width' => '80'],
                'attribute' => 'id',
            ],
            [
                'label' => 'ФИО',
                'attribute' => 'first_name',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->first_name;
                },
            ],
            [
                'label' => 'Курс',
                'attribute' => 'name_course',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->name_course;
                },
            ],
            [
                'label' => 'На каком уроке сейчас',
                'attribute' => 'status_homework',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->status_homework;
                },
            ],
            [
                'label' => 'Платформа',
                'attribute' => 'platform',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->platform;
                },
            ],
            [
                'label' => 'LiveChat',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::button('Написать', ['class' => 'btn btn-warning']);
                },
            ],
            [
                'label' => ' Настройки',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::button('Перейти в карточку', ['class' => 'btn btn-info']);
                },
            ],

//            [
//                'class' => 'yii\grid\ActionColumn',
//                'headerOptions' => ['width' => '50'],
//                'template' => '{view}',
//                'buttons' => [
//                    'view' => function ($url, $model, $key) {
//                        return Html::button('<i class="fas fa-eye"></i>',
//                            [
//                                'class' => 'view_search btn btn-primary',
//                                'data-id' => $key,
//                                'title' => 'Перегляд запиту',
//                            ]);
//                    },
//                ],
//            ],
        ],
    ]); ?>
</div>


<?= \common\widgets\ModalWidget::widget(['id' => 'view_search']); ?>
