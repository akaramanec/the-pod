<?php

use backend\modules\bot\models\Bot;
use backend\modules\customer\models\search\CustomerBloggerSearch;
use backend\modules\media\models\GetImg;
use frontend\models\cart\Cart;
use kartik\daterange\DateRangePicker;
use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Блогеры';
$this->params['breadcrumbs'][] = $this->title;
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
$this->params['right_content'] = '';
$this->params['right_content'] .= Buttons::clearAllFilters();

BootstrapPluginAsset::register($this);
$this->registerJs("
$(document).delegate('#date_from, #date_to', 'change', function() {
    $('#dateFrom').val($('#date_from').val());
    $('#dateTo').val($('#date_to').val());
    $('#statisticDateForm').submit();
});
");

?>
<div class="bg">
    <div class="row">
        <div class="col-md-4">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th width="200">Задолженность:</th>
                    <td><span id="sumDebt">0</span>₴</td>
                </tr>
                <tr>
                    <th width="200">Оплачено:</th>
                    <td><span id="sumTotalPayed">0</span>₴</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <form action="/customer/customer-blogger/pay-blogger-wholesale" method="get">
                <input type="hidden" name="blogger_id" id="input-pay-blogger-wholesale" value="">
                <?= Html::submitButton('Сохранить и оплатить',
                    [
                        'title' => 'Сохранить и оплатить блогерам',
                        'class' => 'btn btn-outline-success btn-pay-blogger-wholesale',
                        'data-pjax' => '0',
                        'data-confirm' => 'Вы уверены, что хотите оплатить блогерам?'
                    ]); ?>
            </form>
        </div>
        <div class="col-md-4">
            <?php \yii\widgets\ActiveForm::begin(['action' => '/customer/customer-blogger', 'method' => 'GET', 'id' => 'statisticDateForm']); ?>
            <?= Html::hiddenInput('dateFrom', '', ['id' => 'dateFrom']); ?>
            <?= Html::hiddenInput('dateTo', '', ['id' => 'dateTo']); ?>
            <?php \yii\widgets\ActiveForm::end() ?>
            <div class="calendar">
                <div class="input-group drp-container">
                    <?= DateRangePicker::widget([
                        'name' => 'statisticDate',
                        'id' => 'statisticDate',
                        'useWithAddon' => true,
                        'convertFormat' => true,
                        'startAttribute' => 'dateFrom',
                        'endAttribute' => 'dateTo',
                        'value' => CustomerBloggerSearch::statisticDate(),
                        'startInputOptions' => [
                            'id' => 'date_from',
                            'value' => CustomerBloggerSearch::statisticDateFrom()
                        ],
                        'endInputOptions' => [
                            'id' => 'date_to',
                            'value' => CustomerBloggerSearch::statisticDateTo()
                        ],
                        'pluginOptions' => [
                            'locale' => ['format' => 'Y-m-d'],
                        ],
                        'options' => ['autocomplete' => 'off']
                    ]) ?>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
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
            'attribute' => 'id',
            'headerOptions' => ['width' => '80'],
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
        [
            'attribute' => 'phone',
            'value' => function ($data) {
                return $data->showPhone;
            },
            'format' => 'raw',
        ],
        [
            'filter' => false,
            'headerOptions' => ['width' => '150'],
            'label' => 'всего рефералов уровня 1',
            'attribute' => 'customerCountTotal',
            'format' => 'raw'
        ],
        [
            'filter' => false,
            'headerOptions' => ['width' => '80'],
            'label' => 'отписалось рефералов уровня 1',
            'attribute' => 'customerCountTotalDisabled',
            'value' => function ($data) {
                return $data->customerCountTotalDisabled;
            },
            'format' => 'raw'
        ],
        [
            'filter' => false,
            'headerOptions' => ['width' => '80'],
            'label' => 'осталось рефералов уровня 1',
            'attribute' => 'customerCountTotalActive',
            'value' => function ($data) {
                return $data->customerCountTotalActive;
            },
            'format' => 'raw'
        ],
        [
            'filter' => false,
            'headerOptions' => ['width' => '120'],
            'label' => 'К-во заказов рефералов уровня 1',
            'attribute' => 'ordersCount',
            'value' => function ($data) {
                return $data->ordersCount;
            },
            'format' => 'raw'
        ],
        [
            'filter' => false,
            'label' => 'Задолженность',
            'attribute' => 'sumDebt',
            'value' => function ($data) {
                return Cart::showPriceStatic($data->blog->sumDebt ?? 0);
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Оплачено',
            'attribute' => 'sumTotalPayed',
            'value' => function ($data) {
                return Cart::showPriceStatic($data->blog->sumTotalPayed ?? 0);
            },
            'format' => 'raw'
        ],
//        [
//            'attribute' => 'platform_id',
//            'headerOptions' => ['width' => '80'],
//            'filter' => Bot::allPlatforms(),
//            'value' => function ($data) {
//                return GetImg::iconBot(Bot::TELEGRAM, '31x31');
//            },
//            'format' => 'raw'
//        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '50'],
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{view}{delete}</div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-eye fa-fw"></i>', ['/customer/customer-blogger/view', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Просмотр',
                        ]);
                },
            ],
        ],
    ],
]); ?>

