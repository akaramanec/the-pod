<?php

use backend\modules\bot\models\Bot;
use backend\modules\media\models\GetImg;
use src\helpers\Common;
use src\helpers\CustomerHelper;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\Customer */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $customer \backend\modules\customer\models\Customer */

$this->title = 'Рефералы';
$this->params['breadcrumbs'][] = $this->title;
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
            'headerOptions' => ['width' => '100'],
            'value' => function ($data) use ($customer) {
                if (isset($customer->blog->customerLevel[$data->id])) {
                    return 'Level ' . $customer->blog->customerLevel[$data->id]['level'];
                }
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'platform_id',
            'headerOptions' => ['width' => '80'],
            'filter' => Bot::allPlatforms(),
            'value' => function ($data) {
                return GetImg::iconBot($data->bot->platform, '31x31');
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'created_at',
            'headerOptions' => ['width' => '150'],
            'format' => 'datetime'
        ],
    ],
]); ?>

