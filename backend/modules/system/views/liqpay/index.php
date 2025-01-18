<?php

use src\helpers\Buttons;

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\LiqpaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Liqpays');
$this->params['breadcrumbs'][] = $this->title;
//$this->params['right_content'] .= Buttons::create();
//$this->params['right_content'] .= \common\widgets\PaginationWidget::widget();
?>

<div class="bg">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table'],
        'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'headerOptions' => ['width' => '80'],
                'attribute' => 'id',
            ],
            'test_public_key',
            'test_private_key',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'headerOptions' => ['width' => '65'],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="far fa-edit"></i>', ['/system/liqpay/update', 'id' => $key],
                            [
                                'class' => 'btn btn-primary',
                                'title' => 'Изменить',
                            ]);

                    },
                ],
            ],
        ],
    ]); ?>
</div>
