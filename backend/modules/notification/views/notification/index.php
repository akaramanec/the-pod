<?php

use src\helpers\Common;
use src\services\Role;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\notification\models\search\BotNotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bot Notifications';
$this->title = 'Уведомления';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= \src\helpers\Buttons::create('Создать');
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'name',
        'text:ntext',
//            'status',

        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '150'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{view} {update} {delete}</div>',
            'visibleButtons' => [
                'update' => true,
                'delete' => function ($model) {
                    if (!Role::check('user-del')) {
                        return false;
                    }
                    return true;
                },
                'view' => false,
            ],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/notification/notification/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);

                },
                'view' => function ($url, $model, $key) {
                    return Html::button('<i class="far fa-eye fa-fw"></i>',
                        [
                            'class' => 'view_customer btn btn-outline-dark',
                            'data-id' => $key,
                            'title' => 'Просмотр',
                        ]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/notification/notification/delete', 'id' => $key],
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

