<?php

use src\helpers\Buttons;
use src\helpers\Common;
use src\helpers\Date;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bot\models\search\BotLoggerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var object $pages */
/** @var $webHookInfo \backend\modules\bot\telegram\TBaseCommon */
$this->title = 'Bot Loggers';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';

$this->params['right_content'] .= Html::button(Date::format_datetime($webHookInfo->getLastErrorDate()), [
    'class' => 'btn btn-secondary',
    'title' => 'Дата ошибки',
]);
$this->params['right_content'] .= Html::button('e', [
    'class' => 'btn btn-secondary',
    'title' => 'Текст последней ошибки: ' . $webHookInfo->getLastErrorMessage(),
]);
$this->params['right_content'] .= Html::button($webHookInfo->getMaxConnections(), [
    'class' => 'btn btn-secondary',
    'title' => 'Максимум соединений за 1 секунду',
]);
$this->params['right_content'] .= Html::button($webHookInfo->getPendingUpdateCount(), [
    'class' => 'btn btn-secondary',
    'title' => 'Сообщений в очереди',
]);
if (Yii::$app->user->can('dev')) {
    $this->params['right_content'] .= Buttons::loggerDeleteAll();
}
?>
<div class="row">
    <div class="col-sm-12">
        <?= LinkPager::widget(ArrayHelper::merge(['pagination' => $pages], Common::pager4())); ?>
        <table class="table table-hover">
            <tbody>
            <?php foreach ($dataProvider as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?php \yii\helpers\VarDumper::dump($item->data, 1000, 5) ?></td>
                    <td><?= $item->slug ?></td>
                    <td><?= Date::format_datetime_all($item->created_at) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>



