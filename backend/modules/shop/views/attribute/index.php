<?php
/**
 * @var object $attribute_value
 * @var $this yii\web\View
 * @var $searchModel backend\modules\shop\models\search\AttributeSearch
 * @var $dataProvider yii\data\ActiveDataProvide
 * @var $model Attribute
 */

use backend\modules\media\models\Img;
use backend\modules\media\widgets\ImgSaveWidget;
use backend\modules\shop\models\Attribute;
use src\helpers\Buttons;
use src\helpers\Common;
use src\services\Role;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Атрибуты';
$this->params['breadcrumbs'][] = $this->title;
$this->params['right_content'] = '';
if (Role::check('dev')) {
    $this->params['right_content'] .= Buttons::create();
}
\backend\assets\UiAsset::register($this);
?>

<div class="row">
    <div class="col-sm-6">
        <h4>Атрибуты</h4>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => false,
            'showHeader' => false,
            'pager' => Common::pager4(),
            'tableOptions' => ['class' => 'table'],
            'rowOptions' => ['class' => 'ui-state-default'],
            'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive sort-attribute', 'id' => 'main-grid'],
            'layout' => "{items}",
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Html::a('<i class="fas fa-sort"></i> ' . $data->name,
                            [
                                '/shop/attribute/index',
                                'id' => $data->id,
                            ], ['class' => Common::activeAttribute($data->id)]);
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['width' => '80'],
                    'template' => '<div class="btn-group base-btn-group float-right" role="group">{update} {attribute_value}</div>',
                    'visibleButtons' => [
                        'attribute_value' => true,
                        'update' => function ($model) {
                            if (!Role::check('attribute-update')) {
                                return false;
                            }
                            return true;
                        }
                    ],
                    'buttons' => [
                        'attribute_value' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-arrow-alt-circle-right"></i>',
                                [
                                    '/shop/attribute/index',
                                    'id' => $model->id,
                                ],
                                [
                                    'class' => 'btn btn-outline-dark',
                                    'title' => 'Значения атрибутов',
                                ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('<i class="far fa-edit"></i>', ['/shop/attribute/update', 'id' => $key],
                                [
                                    'class' => 'btn btn-outline-dark',
                                    'title' => 'Изменить',
                                ]);
                        },
                    ],
                ],
            ],
        ]); ?>

    </div>
    <div class="col-sm-6">
        <?php if ($model): ?>
            <h4>Значения атрибутов</h4>
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider(['query' => $model->getAttributeValue(), 'pagination' => false,]),
                'filterModel' => false,
                'showHeader' => false,
                'pager' => Common::pager4(),
                'tableOptions' => ['class' => 'table'],
                'rowOptions' => ['class' => 'ui-state-attribute-value'],
                'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive sort-attribute-value', 'id' => 'main-grid'],
                'layout' => "{items}",
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->name;
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['width' => '80'],
                        'template' => '<div class="btn-group base-btn-group float-right" role="group">{update}</div>',
                        'visibleButtons' => [
                            'update' => function ($model) {
                                if (!Role::check('attribute-update')) {
                                    return false;
                                }
                                return true;
                            }
                        ],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="far fa-edit"></i>', ['/shop/attribute/update-value', 'id' => $key],
                                    [
                                        'class' => 'btn btn-outline-dark',
                                        'title' => 'Изменить',
                                    ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        <?php endif; ?>
    </div>
</div>


<div hidden>
    <div class="id"><?= Yii::$app->request->get('id') ?></div>
    <div class="attribute_value_id"><?= Yii::$app->request->get('attribute_value_id') ?></div>
</div>
<?php
$script = <<< JS
$('.sort-attribute tbody').attr('id', 'sortable');
$(function () {
    $('#sortable').sortable({
        update: function (event, ui) {
            var x = 1;
            var all_id = [];
            $.each($('.ui-state-default'), function () {
                all_id[x] = $(this).data('key');
                x++;
            });
            $.ajax({
                url: '/shop/attribute/sort',
                data: {sort: JSON.stringify(all_id)},
                type: 'GET',
            });
        },
    });
});

$('.sort-attribute-value tbody').attr('id', 'sortable-value');
$(function () {
    $('#sortable-value').sortable({
        update: function (event, ui) {
            var x = 1;
            var all_id = [];
            $.each($('.ui-state-attribute-value'), function () {
                all_id[x] = $(this).data('key');
                x++;
            });
            $.ajax({
                url: '/shop/attribute/sort-value',
                data: {sort: JSON.stringify(all_id)},
                type: 'GET',
            });
        },
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
