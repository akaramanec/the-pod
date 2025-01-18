<?php

use backend\modules\customer\models\Customer;
use backend\modules\media\models\GetImg;
use kartik\daterange\DateRangePicker;
use src\helpers\Buttons;
use src\helpers\Common;
use src\helpers\DatePeriodSelectorHelper;
use src\services\Role;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\Customer */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateRange array */

$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::clearAllFilters();
$css = <<<CSS
.analytics .calendar {
    position: relative;
}

.analytics .delete-date {
    position: absolute;
    top: 2px;
    right: 50px;
    font-size: 18px;
    padding: 5px 10px;
    color: #ababab;
}
CSS;
$this->registerCss($css);

?>
<div class="row analytics-item-row analytics-filter">
    <div class="col-xl-3 col-lg-4 col-md-5 offset-xl-5 offset-lg-3 offset-md-0">
        <?= DatePeriodSelectorHelper::selectPeriod(); ?>
    </div>
    <div class="col-xl-4 col-lg-5 col-md-7">
        <div class="calendar">
            <?php \yii\widgets\ActiveForm::begin(['action' => '/customer/customer', 'method' => 'GET']) ?>
            <?= Html::hiddenInput('dateFrom', '', ['id' => 'dateFrom']); ?>
            <?= Html::hiddenInput('dateTo', '', ['id' => 'dateTo']); ?>
            <?php \yii\widgets\ActiveForm::end() ?>
            <div class="input-group drp-container">
                <?= DateRangePicker::widget(['name' => 'statisticDate',
                    'id' => 'statisticDate',
                    'useWithAddon' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'from_date',
                    'endAttribute' => 'to_date',
                    'value' => DatePeriodSelectorHelper::getDateRage($dateRange['dateFrom'] ?? null, $dateRange['dateTo'] ?? null),
                    'startInputOptions' => ['id' => 'date_from',
                        'value' => DatePeriodSelectorHelper::getDateFrom($dateRange['dateFrom'] ?? null)],
                    'endInputOptions' => ['id' => 'date_to',
                        'value' => DatePeriodSelectorHelper::getDateTo($dateRange['dateTo'] ?? null)],
                    'pluginOptions' => ['locale' => ['format' => 'd.m.Y'],]]) ?>
                <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                </div>
            </div>
        </div>

    </div>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        [
            'visible' => Yii::$app->user->can('dev'),
            'attribute' => 'id',
            'headerOptions' => ['width' => '100'],
            'format' => 'raw'
        ],
        [
            'label' => false,
            'contentOptions' => ['class' => 'img-table-circle'],
            'headerOptions' => ['width' => '59', 'class' => 'header-name-table'],
            'value' => function ($data) {
                return GetImg::customer($data, '59x59');
            },
            'format' => 'raw'
        ],

        'first_name',
        'last_name',
        'username',
        [
            'label' => 'Блогер',
            'filter' => Customer::listBlogger(),
            'attribute' => 'parent_id',
            'value' => function ($data) {
                return Customer::fullNameParent($data);
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'phone',
            'value' => function ($data) {
                return $data->showPhone;
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '100'],
            'filter' => Customer::statusesBloggerAll(),
            'attribute' => 'blogger',
            'value' => function ($data) {
                return Customer::statusBlogger($data->blogger);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'status',
            'headerOptions' => ['width' => '40'],
            'filter' => Customer::statusesAll(),
            'value' => function ($data) {
                return Customer::status($data->status);
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Особые метки',
            'attribute' => 'customerMark',
            'headerOptions' => ['width' => '80'],
            'filter' => Customer::customerMarks(),
            'value' => function ($data) {
                if ($data->black_list) {
                    return $data->blackList($data->black_list) ;
                } elseif ($data->regular_customer) {
                    return $data->regularCustomer($data->regular_customer) ;
                }
                return '';
            },
            'format' => 'raw'
        ],
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
                'view' => true,
            ],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/customer/customer/update', 'id' => $key],
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
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/customer/customer/delete', 'id' => $key],
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
]);?>
<?= \common\widgets\ModalWidget::widget(['id' => 'view_customer']); ?>