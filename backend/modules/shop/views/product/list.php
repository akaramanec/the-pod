<?php
/**
 * @var $base_id         integer
 * @var $this            \yii\web\View
 * @var $searchModel     \backend\modules\shop\models\search\ProductSearch
 * @var $dataProvider    yii\data\ActiveDataProvider
 */

use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Products list');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
$( ".search_form input, .search_form select" ).change(function() {
    $("#search-form").submit();
});
$(\'body\').on(\'click\',\'a.del-product\',function(e){
    e.preventDefault();
    
    var warning_text = "' . Yii::t('app', 'Are you sure you want to delete this product?') . '";

    var conf = confirm( warning_text );
    if(conf) {
        var id = $(this).attr(\'href\');
        var base_id = $(this).attr(\'data-base-id\');
        $.ajax({
            url: "' . \yii\helpers\Url::toRoute(['product/delete'], true) . '",
            type: \'post\',
            data: {
                \'id\': id,
                \'base_id\': base_id
            },
            success: function (result) {
                if (result) {
                    $(\'tr[data-key = \' + id + \']\').detach();
                } else {
                    alert(\'error change status\');
                }
            }
        });
    }

});
');

?>
<?php Pjax::begin(); ?>
<div id="product">
    <div class="content-detached content-left">
        <div class="content-body block-table">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <!-- Task List table -->
                                <?php $pageSizeWidget = \nterms\pagesize\PageSize::widget([
                                    'options' => ['class' => 'selectBox'],
                                    'label' => Yii::t('app', 'on page'),
                                    'defaultPageSize' => 10, 'sizes' => [10 => 10, 50 => 50, 200 => 200, 500 => 500],
                                ]);
                                ?>

                                <p class="mb-1">
                                    <?= Yii::t('app', 'INFO_TABLE') ?>
                                </p>
                                <?= GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'layout' => '<div class="row mb-1"><div class="col-lg-3">' . $pageSizeWidget . '</div><div class="col-lg-6">{summary}</div><div class="col-lg-3"><a href="' . Url::to(['product/change', 'base_id' => $base_id, 'action' => 'add', 'last_page' => $page]) . '" class="btn btn-success btn-min-width btn-sm box-shadow-1 float-right btn__add-product"><i class="fa fa-plus"></i> ' . Yii::t('app',
                                            'Add product') . '</a></div></div>{items}<div class="row"><div class="col-lg-3">{summary}</div><div class="col-lg-6 text-right">{pager}</div><div class="col-lg-3"><a href="' . Url::to(['product/change', 'base_id' => $base_id, 'action' => 'add', 'last_page' => $page]) . '" class="btn btn-success btn-min-width btn-sm box-shadow-1 float-right btn__add-product"><i class="fa fa-plus"></i> ' . Yii::t('app',
                                            'Add product') . '</a></div></div>',
                                    'filterSelector' => 'select[name="per-page"]',
                                    'footableOptions' => [
                                        'showToggle' => true, 'expandAll' => true,
                                        'breakpoints' => [
                                            'xs' => 480,
                                            'sm' => 720,
                                            'md' => 992,
                                            'lg' => 1200,
                                            'xl' => 1400,
                                        ],
                                    ],
                                    'headerRowOptions' => ['class' => 'row-column-middle'],
                                    'rowOptions' => ['class' => 'row-column-middle'],
                                    'pager' => [
                                        'prevPageLabel' => Yii::t('app', 'Previous'),
                                        'nextPageLabel' => Yii::t('app', 'Next'),
                                    ],
                                    'tableOptions' => ['class' => "table table-striped table-bordered"],
                                    'columns' =>
                                        [
                                            [
                                                'label' => Yii::t('app', 'Sort'),
                                                'format' => 'integer',
                                                'headerOptions' => ['data-hide' => "phone,sm,md"],
                                                'attribute' => 'sort',
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Name'),
                                                'format' => 'raw',
                                                'attribute' => 'name',
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Price'),
                                                'format' => 'text',
                                                'attribute' => 'price',
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Quantity'),
                                                'format' => 'text',
                                                'attribute' => 'qty_total',
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Image'),
                                                'format' => 'raw',
                                                'attribute' => 'image',
                                                'headerOptions' => ['data-hide' => "phone"],

                                                'value' => function ($searchModel) {
                                                    if ($searchModel->image && file_exists(__DIR__ . '/../../web/' . $searchModel->image)) {
                                                        return '<img width="50px" src="' . $searchModel->image . '" />';
                                                    }
                                                },
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Category'),
                                                'format' => 'raw',
                                                'attribute' => 'product_categories',
                                                'headerOptions' => ['data-hide' => "phone,xs,sm,md,lg"],
                                                'value' => function ($searchModel) {
                                                    return $searchModel->category->name;
                                                },
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Status'),
                                                'format' => 'raw',
                                                'attribute' => 'status',
                                                'headerOptions' => ['data-hide' => "phone,sm"],

                                                'value' => function ($searchModel) {
                                                    return Product::getStatus($searchModel);
                                                },
                                            ],
                                            [
                                                'label' => Yii::t('app', 'Actions'),
                                                'format' => 'raw',
                                                'headerOptions' => ['data-hide' => "phone"],
                                                'value' => function ($searchModel) use ($page) {
                                                    $buttons = '';
                                                    if ($searchModel->id != 0) {
                                                        $buttons = '<div class="form-group text-center"><div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';

                                                        $buttons .= Html::a('<i class="la la-edit" aria-hidden="true"></i>',
                                                            Url::to(['product/change', 'base_id' => $searchModel->base_id, 'id' => $searchModel->id, 'action' => 'edit', 'last_page' => $page]),
                                                            [
                                                                'class' => 'btn btn-info', 'data-toggle' => "tooltip", 'data-placement' => "top", 'title' => Yii::t('app',
                                                                'Edit'),
                                                            ]);

                                                        $buttons .= Html::a('<i class="la la-copy"></i>',
                                                            Url::to(['product/change', 'base_id' => $searchModel->base_id, 'id' => $searchModel->id, 'action' => 'pclone', 'last_page' => $page]),
                                                            [
                                                                'class' => 'btn btn-info', 'data-toggle' => "tooltip", 'data-placement' => "top", 'title' => Yii::t('app',
                                                                'Clone'),
                                                            ]);

                                                        $buttons .= Html::a('<i class="la la-trash" aria-hidden="true"></i>',
                                                            $searchModel->id,
                                                            [
                                                                'class' => 'btn btn-danger del-product', 'data-base-id' => $searchModel->base_id, 'data-toggle' => "tooltip", 'data-placement' => "top", 'title' => Yii::t('app',
                                                                'Delete'),
                                                            ]);

                                                        $buttons .= '</div></div>';
                                                    }


                                                    return $buttons;
                                                },

                                            ],
                                        ],

                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="sidebar-detached sidebar-right">
        <button class="show-sidebar"></button>
        <div class="sidebar">
            <div class="bug-list-sidebar-content">
                <!-- Predefined Views -->
                <div class="card fix-form">
                    <div class="card-header">
                        <h4 class="card-title"><?= Yii::t('app', 'Filter') ?></h4>
                    </div>
                    <div class="card-body border-top-blue-grey border-top-lighten-5">
                        <div class="bug-list-search">
                            <div class="bug-list-search-content">
                                <?php $form = \yii\bootstrap\ActiveForm::begin([
                                    'method' => 'get',
                                    'id' => 'search-form',
                                    'action' => Url::to(['product/list', 'base_id' => $base_id]),
                                ]); ?>
                                <?= $form->field($searchModel, 'q', [
                                    'template' => "
                                            <div class=\"position-relative\">
                                            {input}
                                            <div class=\"form-control-position\">
                                                <i class=\"la la-search text-size-base text-muted\"></i>
                                            </div>
                                        </div>",

                                ])->label('')->textInput([
                                    'placeholder' => Yii::t('app', 'Search product...'),
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <!-- /contacts search -->
                    <?= $form->field($searchModel, 'sstatus',
                        ['options' => ['class' => 'form-group col-lg-6 col-xs-12']])
                        ->radioList(Helper::getStatuses(), ['class' => 'search_form'])->label(Yii::t('app',
                            'Status')); ?>
                    <?= $form->field($searchModel, 'product_category',
                        ['options' => ['class' => 'form-group col-lg-12 col-xs-12 search_form']])
                        ->dropDownList(Category::getAllCategories($base_id))->label(Yii::t('app',
                            'Category')); ?>
                </div>
                <?php \yii\bootstrap\ActiveForm::end(); ?>
                <!--/ Predefined Views -->
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
