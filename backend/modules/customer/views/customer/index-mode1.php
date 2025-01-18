<?php

use src\helpers\Buttons;
use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel backend\modules\customer\models\search\Customer */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
$this->params['right_content'] .= \common\widgets\PaginationWidget::widget();
?>
<div class="row content-admin">
    <div class="col-md-8">
        <div class="bg">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table'],
                'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive'],
                'pager' => Common::pager4(),
                'id' => 'customers',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'headerOptions' => ['width' => '80'],
                        'label' => 'ID',
                        'attribute' => 'view_id',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->view_id;

                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Name'),
                        'attribute' => 'first_name',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->first_name;

                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Phone'),
                        'attribute' => 'phone',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->phone;

                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Email'),
                        'attribute' => 'email',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->email;
                        }
                    ],
                    [
                        'headerOptions' => ['width' => 75],
                        'label' => Yii::t('app', 'Chats'),
                        'attribute' => 'icons_platform',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->icons_platform;

                        }
                    ],
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'contentOptions' => ['class' => 'send'],
                        'headerOptions' => ['width' => 65],
                        'name' => 'id',
                        'cssClass' => 'add-to-relations',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return ['value' => $model->id];
                        },
                        'header' => Html::tag('span', 'Группа',
                            [
                                'class' => 'head_relations',
                                'title' => Yii::t('app', 'Link Profiles')
                            ]),
                    ],
                    [
                        'headerOptions' => ['width' => 65],
                        'label' => '',
                        'attribute' => 'action',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->action;
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
</div>



