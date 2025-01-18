<?php

use backend\modules\customer\models\BloggerWithdrawalRequest;
use src\helpers\Common;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\BloggerWithdrawalRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blogger Withdrawal Requests';
$this->params['breadcrumbs'][] = $this->title;


$this->title = 'Запросы на виплату';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
//$this->params['right_content'] .= \src\helpers\Buttons::create('Добавить рассылку');
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
            'attribute' => 'bot_customer_first_name',
            'format' => 'raw',
            'value' => function (BloggerWithdrawalRequest $model) {
                return $model->botCustomer->first_name;
            },
        ],
        [
            'attribute' => 'bot_customer_last_name',
            'format' => 'raw',
            'value' => function (BloggerWithdrawalRequest $model) {
                return $model->botCustomer->last_name;
            },
        ],
        [
            'attribute' => 'bot_customer_username',
            'format' => 'raw',
            'value' => function (BloggerWithdrawalRequest $model) {
                return $model->botCustomer->username;
            },
        ],
        [
            'attribute' => 'sum',
            'label' => 'Сума',
            'format' => 'raw',
        ],
        [
            'attribute' => 'card_number',
            'label' => 'Карта',
            'format' => 'raw',
            'value' => function (BloggerWithdrawalRequest $model) {
                return $model->botCustomerCard->number;
            },
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'headerOptions' => ['width' => '150'],
        ],
//        [
//            'class' => 'yii\grid\ActionColumn',
//            'template' => '<div class="btn-group base-btn-group float-right" role="group">{delete}</div>',
//            'headerOptions' => ['width' => '100'],
//            'buttons' => [
//                'delete' => function ($url, $model, $key) {
//                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/customer/newsletter/delete', 'id' => $key],
//                        [
//                            'title' => 'Удалить',
//                            'class' => 'btn btn-outline-dark',
//                            'data-method' => 'post',
//                            'data-pjax' => '0',
//                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
//                        ]);
//                },
//            ],
//        ],
    ],
]); ?>
