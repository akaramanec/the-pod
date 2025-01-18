<?php
/**
 * @var $this \yii\web\View
 * @var Statistic $statistic
 */

use backend\modules\customer\models\Statistic;
use kartik\daterange\DateRangePicker;
use yii\bootstrap4\Progress;

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\Statistic::register($this);
?>
<div id="statistic">
    <div class="row">
        <div class="col-md-3 offset-md-9">
            <div class="calendar">
                <div class="input-group drp-container">
                    <?= DateRangePicker::widget([
                        'name' => 'statisticDate',
                        'id' => 'statisticDate',
                        'useWithAddon' => true,
                        'convertFormat' => true,
                        'startAttribute' => 'from_date',
                        'endAttribute' => 'to_date',
                        'value' => Statistic::statisticDate(),
                        'startInputOptions' => [
                            'id' => 'date_from',
                            'value' => Statistic::statisticDateFrom()
                        ],
                        'endInputOptions' => [
                            'id' => 'date_to',
                            'value' => Statistic::statisticDateTo()
                        ],
                        'pluginOptions' => [
                            'locale' => ['format' => 'd.m.Y'],
                        ]
                    ]) ?>
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
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="user-icon-info">
                <p class="float-left text-center"><i class="fas fa-user-alt"></i></p>
                <p><span><?= $statistic->countAllCustomer ?></span></p>
                <p>Полная регистрация</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="user-icon-info">
                <p class="float-left text-center"><i class="fas fa-user-plus"></i></p>
                <p><span><?= $statistic->countNewCustomer ?></span></p>
                <p>За выбранный период</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="user-icon-info">
                <p class="float-left text-center"><i class="fas fa-mobile-alt"></i></p>
                <p><span><?= $statistic->countAllCustomerSubscribed ?></span></p>
                <p>Только подписался</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="user-icon-info">
                <p class="float-left text-center"><i class="fas fa-mobile-alt"></i></p>
                <p><span><?= $statistic->countNewCustomerSubscribed ?></span></p>
                <p>Только подписался за выбранный период</p>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="bg" id="chartdiv"></div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="bg">
                <?php if ($statistic->clickCount): ?>
                    <div class="click-block">
                        <h4 class="mb-2">Частота нажатия на кнопки</h4>
                        <?php foreach ($statistic->clickStatistic as $clickIndex => $click): ?>
                            <div class="click-item">
                                <p><?= $click['name'] ?><span class="float-right"><?= $click['count'] ?></span></p>
                                <?= Progress::widget(['percent' => $click['percent'],
                                    'barOptions' => ['class' => 'bg' . $clickIndex]]); ?>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div id="data_for_am_charts" hidden><?= $statistic->amCharts ?></div>
