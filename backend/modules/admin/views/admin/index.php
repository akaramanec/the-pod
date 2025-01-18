<?php
/** @var \yii\data\ActiveDataProvider $dataProvider */

/** @var \backend\modules\admin\models\AuthAdminSearch $searchModel */

use backend\modules\admin\models\AuthAdmin;
use backend\modules\admin\models\AuthItem;
use backend\modules\media\models\Img;
use src\helpers\Buttons;

use src\helpers\Common;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Админы';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['right_content'] = '';
$this->params['right_content'] = Buttons::create();

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
    'columns' => [
        [
            'headerOptions' => ['width' => '60'],
            'attribute' => 'id',
        ],
        [
            'label' => false,
            'format' => 'raw',
            'contentOptions' => ['class' => 'img-table-circle'],
            'headerOptions' => ['width' => '59', 'class' => 'header-name-table'],
            'value' => function ($data) {
                return Img::main(ADMIN, $data->id, $data->img, '400x400');
            },
        ],
        'surname',
        'name',
        [
            'headerOptions' => ['width' => '155'],
            'attribute' => 'phone',
        ],
        'email:email',
        [
            'headerOptions' => ['width' => '140'],
            'filter' => AuthAdmin::statusesAll(),
            'attribute' => 'status',
            'value' => function ($data) {
                return AuthAdmin::status($data->status);
            },
            'format' => 'raw',
        ],
        [
            'headerOptions' => ['width' => '140'],
            'attribute' => 'roles',
            'filter' => false,
            'value' => function ($data) {
                return AuthItem::getRolesAsString($data->roles);
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'created_at',
            'format' => ['datetime']
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {delete}</div>',
            'headerOptions' => ['width' => '100'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::a('<i class="far fa-edit"></i>', ['/admin/admin/update', 'id' => $key],
                        [
                            'class' => 'btn btn-outline-dark',
                            'title' => 'Изменить',
                        ]);
                },
//                'delete' => function ($url, $model, $key) {
//                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/admin/admin/delete', 'id' => $key],
//                        [
//                            'title' => 'Удалить',
//                            'class' => 'btn btn-outline-dark',
//                            'data-method' => 'post',
//                            'data-pjax' => '0',
//                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
//                        ]);
//                },
            ],
        ],
    ],
]); ?>






