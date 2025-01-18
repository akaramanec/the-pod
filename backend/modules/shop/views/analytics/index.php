<?php

use backend\modules\shop\service\analytics\AnalyticsOrderService;
use backend\modules\shop\service\analytics\components\general\AverageCountOrder;
use backend\modules\shop\service\analytics\components\general\AverageSumOrder;
use backend\modules\shop\service\analytics\components\general\MaxSumOrder;
use backend\modules\shop\service\analytics\components\general\SuccessOrder;
use backend\modules\shop\service\analytics\components\general\SumSuccessOrder;
use backend\modules\shop\service\analytics\components\general\UsersAll;
use backend\modules\shop\service\analytics\components\general\UsersUnique;
use backend\modules\shop\service\analytics\components\general\UsersUnsubscribed;
use backend\modules\shop\service\analytics\components\newsletter\MoreFiveOrder;
use backend\modules\shop\service\analytics\components\newsletter\OneOrder;
use backend\modules\shop\service\analytics\components\newsletter\ToFiveOrder;
use backend\modules\shop\service\analytics\components\orderProcessing\ManagerOrderProcessing;
use backend\modules\shop\service\analytics\components\status\AnalyticsStatusItemModel;
use backend\modules\shop\service\analytics\enum\AnalyticsGeneralTypeEnum;
use backend\modules\shop\service\analytics\enum\AnalyticsNewsletterIndicatorTypeEnum;
use src\helpers\DatePeriodSelectorHelper;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $orderAnalytics AnalyticsOrderService */
/** @var $jsChart string */

\backend\modules\shop\assets\AnalyticsAssets::register($this);

$this->title = 'Аналитика ('
    . date('d.m.Y', strtotime($orderAnalytics->getDateFrom()))
    . ' - '
    . date('d.m.Y', strtotime($orderAnalytics->getDateTo()))
    . ')';
$this->params['breadcrumbs'][] = 'Аналитика';

/** @var AverageSumOrder $averageSumOrder */
$averageSumOrder = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::AVERAGE_SUM_ORDER);

/** @var AverageCountOrder $averageSumOrder */
$averageCountOrder = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::AVERAGE_COUNT_ORDER);

/** @var AverageCountOrder $averageSumOrder */
$lvtOrder = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::LVT_ORDER);

/** @var MaxSumOrder $averageSumOrder */
$maxSumOrder = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::MAX_SUM_ORDER);

/** @var SuccessOrder $averageSumOrder */
$successOrder = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::SUCCESS_ORDER);

/** @var SumSuccessOrder $averageSumOrder */
$sumSuccessOrder = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::SUM_SUCCESS_ORDER);

/** @var UsersAll $averageSumOrder */
$usersAll = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::USERS_ALL);

/** @var UsersUnique $averageSumOrder */
$usersUnique = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::USERS_UNIQUE);

/** @var UsersUnsubscribed $averageSumOrder */
$usersUnsubscribed = $orderAnalytics->getGeneralIndicators(AnalyticsGeneralTypeEnum::USERS_UNSUBSCRIBED);

/** @var OneOrder $newsletterOneOrder */
$newsletterOneOrder = $orderAnalytics->getNewsletterIndicators(AnalyticsNewsletterIndicatorTypeEnum::ONE);

/** @var ToFiveOrder $newsletterOneOrder */
$newsletterToFiveOrder = $orderAnalytics->getNewsletterIndicators(AnalyticsNewsletterIndicatorTypeEnum::TO_FIVE);

/** @var MoreFiveOrder $newsletterOneOrder */
$newsletterMoreFiveOrder = $orderAnalytics->getNewsletterIndicators(AnalyticsNewsletterIndicatorTypeEnum::MORE_FIVE);

/** @var  ManagerOrderProcessing $managerOrderProcessing */
$managerOrderProcessing = $orderAnalytics->getManagerOrderProcessing();

?>


    <div class="analytics">

        <div class="row analytics-item-row analytics-filter">
            <div class="col-xl-3 col-lg-4 col-md-5 offset-xl-5 offset-lg-3 offset-md-0">
                <?= DatePeriodSelectorHelper::selectPeriod(); ?>
            </div>
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="calendar">
                    <?php \yii\widgets\ActiveForm::begin(['action' => '/shop/analytics', 'method' => 'GET']) ?>
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
                            'value' => $orderAnalytics->getDateRage(),
                            'startInputOptions' => ['id' => 'date_from',
                                'value' => $orderAnalytics->getDateFrom()],
                            'endInputOptions' => ['id' => 'date_to',
                                'value' => $orderAnalytics->getDateTo()],
                            'pluginOptions' => ['locale' => ['format' => 'd.m.Y'],]]) ?>
                        <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        </div>
                    </div>
                    <a href="/customer/statistic/delete-statistic-date" class="delete-date"><i class="fas fa-times"></i></a>
                </div>

            </div>
        </div>
        <div class="row analytics-item-row analytics-indicators analytics-general-indicators">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $averageSumOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $maxSumOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $averageCountOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $lvtOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12">
                <?= $successOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12">
                <?= $sumSuccessOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $usersUnique->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $usersAll->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $usersUnsubscribed->getCartHtml(); ?>
            </div>
        </div>

     <!--   <div class="row analytics-item-row analytics-indicators analytics-general-indicators">

        </div>-->

        <div class="row analytics-item-row  analytics-table  analytics-order-processing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <i class="fas fa-info-circle general-info-icon" title="<?=AnalyticsStatusItemModel::getTableDescription()?>"></i>
                    <div class="card-header">
                        <div class="card-title">
                            <span>Обработка заказов</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <?php foreach (ManagerOrderProcessing::getTableHeaderTitles() as $trItem): ?>
                                <tr>
                                    <?php foreach ($trItem as $thTitle => $thOptions): ?>
                                        <?= Html::tag('th', $thTitle, $thOptions); ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            </thead>
                            <tbody>
                            <?php if (!empty($managerOrderProcessing->managerItemsList)) : ?>
                                <?php foreach ($managerOrderProcessing->managerItemsList as $key => $item): ?>
                                    <?php if ($item->isVisible) : ?>
                                        <tr>
                                            <th scope="row"><?= $key + 1; ?></th>
                                            <td class=""><?= $item->managerName; ?></td>
                                            <td class="text-center"><?= $item->countAllOrders; ?></td>
                                            <td class="text-center"><?= $item->timeInProcessingStatus ?: '-'; ?></td>
                                            <td class="text-center"><?= $item->timeInWorkStatus ?: '-'; ?></td>
                                            <td class="text-center"><?= $item->timeSuccessStatus ?: '-'; ?></td>
                                            <td class="text-center"><?= $item->getPercentCanceledOrder(); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row analytics-item-row analytics-table analytics-status-processing">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card">
                    <i class="fas fa-info-circle general-info-icon" title="<?=ManagerOrderProcessing::getTableDescription()?>"></i>
                    <div class="card-header">
                        <div class="card-title">
                            <span>Статусы</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <?php foreach (AnalyticsStatusItemModel::getTableHeaderTitles() as $title => $options) : ?>
                                    <?= Html::tag('th', $title, $options); ?>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orderAnalytics->getStatus() as $key => $item) : ?>
                                <tr>
                                    <th scope="row"><?= $key + 1 ?></th>
                                    <td class=""><?= $item->getTitle() ?></td>
                                    <td class="text-center"><?= $item->getCount() ?></td>
                                    <td class="text-center"><?= $item->getPercent() ?> %</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <canvas id="chart_order" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row analytics-item-row analytics-indicators analytics-newsletter-indicators">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $newsletterOneOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $newsletterToFiveOrder->getCartHtml(); ?>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <?= $newsletterMoreFiveOrder->getCartHtml(); ?>
            </div>
        </div>
    </div>
<?php
$this->registerJs($orderAnalytics->getChartJs());