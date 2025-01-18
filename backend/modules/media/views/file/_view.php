<?php
/**
 * @var integer $entity
 * @var integer $model_id
 */

use src\helpers\Common;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use backend\modules\media\models\Files;

?>


<?= GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => Files::find()->where(['entity' => $entity])->andWhere(['entity_id' => $model_id]),
        'pagination' => false
    ]),
    'pager' => Common::pager4(),
    'tableOptions' => ['class' => 'table'],
    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive'],
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['width' => '80'],
        ],
        'name',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'headerOptions' => ['width' => '100'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return Html::button('<i class="fas fa-pencil-alt"></i>',
                        [
                            'class' => 'btn btn-primary edit_file',
                            'title' => 'Изменить',
                            'data-toggle' => 'modal',
                            'data-target' => '#edit_file',
                            'title' => 'Изменить название файла',
                            'data-id' => $key,
                        ]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fas fa-trash-alt"></i>', ['/media/file/delete', 'id' => $key],
                        [
                            'title' => 'Удалить',
                            'class' => 'btn btn-danger',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                        ]);
                },
            ],
        ],
    ],
]); ?>







